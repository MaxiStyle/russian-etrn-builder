<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DateTimeInterface;
use DOMDocument;
use DOMElement;
use DOMException;
use MaxiStyle\EtrnBuilder\Entities;
use MaxiStyle\EtrnBuilder\Entities\Address;
use MaxiStyle\EtrnBuilder\Entities\DangerousCargoInfo;
use MaxiStyle\EtrnBuilder\Entities\DocumentRequisites;
use MaxiStyle\EtrnBuilder\Entities\HumidityRegime;
use MaxiStyle\EtrnBuilder\Entities\InfoField;
use MaxiStyle\EtrnBuilder\Entities\OrderCargo;
use MaxiStyle\EtrnBuilder\Entities\OrderSignatory;
use MaxiStyle\EtrnBuilder\Entities\Participant;
use MaxiStyle\EtrnBuilder\Entities\Person;
use MaxiStyle\EtrnBuilder\Entities\RoutePoint;
use MaxiStyle\EtrnBuilder\Entities\SupplyPoint;
use MaxiStyle\EtrnBuilder\Entities\TemperatureRegime;
use MaxiStyle\EtrnBuilder\Entities\VehicleRequirements;

/**
 * Билдер файла обмена «Информация грузоотправителя» ЭЗЗ T1.
 * КНД 1110361, префикс имени файла ON_ZAKZVGO.
 *
 * Порядок элементов внутри Документа — строго по xs:sequence из examples/order_t1.xsd.
 *
 * Методы блоков УчастникТип / АдресТип / АдресПользТип / Подписант ЭЗЗ / форматтеры сделаны
 * protected, чтобы {@see OrderT2Builder} мог их переиспользовать.
 */
class OrderT1Builder extends DocumentBuilder implements DocumentBuilderInterface
{
    /**
     * @throws DOMException
     */
    public function build(DOMDocument $dom, DOMElement $parent, object $doc): void
    {
        if (!$doc instanceof Entities\OrderT1) {
            throw new \InvalidArgumentException('Документ должен быть экземпляром OrderT1');
        }

        $this->validate($doc);

        // Файл (корневой)
        $parent->setAttribute('ИдФайл', $doc->fileId ?? '');
        $parent->setAttribute('ВерсПрог', $doc->softwareVersion ?? '');
        $parent->setAttribute('ВерсФорм', $doc->formatVersion);
        foreach ($doc->otherRecipientIds as $id) {
            $this->append($dom, $parent, 'ИдПолИной', (string)$id);
        }

        // Документ
        $document = $dom->createElement('Документ');
        $document->setAttribute('КНД', $doc->knd);
        $document->setAttribute('Функция', $doc->function);
        if ($doc->dateFile !== null) {
            $document->setAttribute('ДатИнфГО', $doc->dateFile->format('d.m.Y'));
            $document->setAttribute('ВрИнфГО', $doc->dateFile->format('H:i:s'));
        }
        $document->setAttribute('НаимЭкСубСост', $doc->composerName);

        if ($doc->composerBasis !== null) {
            $this->append($dom, $document, 'ОснДовОргСост', $doc->composerBasis);
        }
        if ($doc->contract !== null) {
            $this->appendRequisites($dom, $document, 'ДогОргПрвз', $doc->contract);
        }

        // СодИнфГО
        $sodInfGo = $dom->createElement('СодИнфГО');
        $this->setAttributeIf($sodInfGo, 'УИД_Зак', $doc->documentUuid);
        $sodInfGo->setAttribute('СодОпер', $doc->operationContent);
        $sodInfGo->setAttribute('НомЗак', $doc->orderNumber);
        if ($doc->orderDate !== null) {
            $sodInfGo->setAttribute('ДатаЗак', $doc->orderDate->format('d.m.Y'));
        }
        $sodInfGo->setAttribute('УкНормПрвз', $doc->regulatoryInstructions);
        $this->setAttributeIf($sodInfGo, 'ПредСрПрвз', $doc->deadline);
        $sodInfGo->setAttribute('ПрвзПищПрод', $doc->foodTransport ? '1' : '0');
        $this->setAttributeIf($sodInfGo, 'ИныеУслв', $doc->otherConditions);
        $this->setAttributeIf($sodInfGo, 'КонПунктМрш', $doc->routeEndPoint);

        // Дочерние СодИнфГО (порядок по xs:sequence):
        // ПромПунктМрш*, СвГО, СвПрв, СвЛицГО*, ПунктПод, АдрПункт+, ОпГруз+, ПарТСПрвз, ТемпРеж?, ВлажнРеж?, ИнфПол*
        foreach ($doc->routeIntermediatePoints as $point) {
            $this->append($dom, $sodInfGo, 'ПромПунктМрш', (string)$point);
        }

        $this->appendParticipant($dom, $sodInfGo, 'СвГО', $doc->shipper);
        $this->appendParticipant($dom, $sodInfGo, 'СвПрв', $doc->carrier);

        foreach ($doc->shipperPersons as $person) {
            $this->appendShipperPerson($dom, $sodInfGo, $person);
        }

        if ($doc->supplyPoint !== null) {
            $this->appendSupplyPoint($dom, $sodInfGo, $doc->supplyPoint);
        }

        foreach ($doc->routePoints as $point) {
            $this->appendRoutePoint($dom, $sodInfGo, $point);
        }

        foreach ($doc->cargos as $cargo) {
            $this->appendCargo($dom, $sodInfGo, $cargo);
        }

        if ($doc->vehicleRequirements !== null) {
            $this->appendVehicleRequirements($dom, $sodInfGo, $doc->vehicleRequirements);
        }
        if ($doc->temperature !== null) {
            $this->appendTemperature($dom, $sodInfGo, $doc->temperature);
        }
        if ($doc->humidity !== null) {
            $this->appendHumidity($dom, $sodInfGo, $doc->humidity);
        }
        foreach ($doc->infoFields as $field) {
            $this->appendInfoField($dom, $sodInfGo, $field);
        }

        $document->appendChild($sodInfGo);

        if ($doc->signatory !== null) {
            $this->appendOrderSignatory($dom, $document, 'ПодпИнфГО', $doc->signatory);
        }

        $parent->appendChild($document);
    }

    /**
     * Бизнес-правила: единственный идентификатор у shipper/carrier, ≥1 АдрПункт Погрузки и
     * ≥1 Выгрузки, contract при Функция=Заявка, ОргВладИнфр на каждом пункте Погрузки.
     */
    protected function validate(Entities\OrderT1 $doc): void
    {
        if ($doc->shipper === null) {
            throw new \InvalidArgumentException('OrderT1: обязательно поле shipper (СвГО)');
        }
        if ($doc->carrier === null) {
            throw new \InvalidArgumentException('OrderT1: обязательно поле carrier (СвПрв)');
        }
        $this->assertParticipantIdentity($doc->shipper, 'shipper');
        $this->assertParticipantIdentity($doc->carrier, 'carrier');

        if ($doc->function === 'Заявка' && $doc->contract === null) {
            throw new \InvalidArgumentException('OrderT1: при Функция=Заявка обязателен contract (ДогОргПрвз)');
        }

        $hasLoading = $hasUnloading = false;
        foreach ($doc->routePoints as $point) {
            if ($point->operation === 'Погрузка') {
                $hasLoading = true;
                if (empty($point->infrastructureOwnerName) || empty($point->infrastructureOwnerInn)) {
                    throw new \InvalidArgumentException(
                        'OrderT1: у пункта Погрузки обязательны infrastructureOwnerName и infrastructureOwnerInn (ОргВладИнфр)'
                    );
                }
            } elseif ($point->operation === 'Выгрузка') {
                $hasUnloading = true;
            }
        }
        if (!$hasLoading || !$hasUnloading) {
            throw new \InvalidArgumentException('OrderT1: требуется минимум один АдрПункт Погрузки и один Выгрузки');
        }

        if (empty($doc->cargos)) {
            throw new \InvalidArgumentException('OrderT1: требуется минимум один ОпГруз');
        }
        if ($doc->vehicleRequirements === null) {
            throw new \InvalidArgumentException('OrderT1: обязательно поле vehicleRequirements (ПарТСПрвз)');
        }
        if ($doc->signatory === null) {
            throw new \InvalidArgumentException('OrderT1: обязательно поле signatory (ПодпИнфГО)');
        }
    }

    protected function assertParticipantIdentity(Participant $p, string $context): void
    {
        $count = ((int)($p->legalEntity !== null))
            + ((int)($p->ip !== null))
            + ((int)($p->individual !== null))
            + ((int)($p->foreign !== null));
        if ($count !== 1) {
            throw new \InvalidArgumentException(
                "OrderT1 [{$context}]: должен быть задан ровно один из legalEntity/ip/individual/foreign, задано: {$count}"
            );
        }
    }

    /**
     * Строит блок УчастникТип: ИнфДляУчаст* + ИдСв + Адрес? + Конт.
     */
    protected function appendParticipant(DOMDocument $dom, DOMElement $parent, string $name, Participant $p): void
    {
        $el = $dom->createElement($name);

        foreach ($p->extraInfo as $info) {
            $infoEl = $dom->createElement('ИнфДляУчаст');
            $infoEl->setAttribute('ИННУчаст', (string)($info['inn'] ?? ''));
            $infoEl->setAttribute('ИнфУчаст', (string)($info['text'] ?? ''));
            $el->appendChild($infoEl);
        }

        $this->appendIdSv($dom, $el, $p);

        if ($p->address !== null) {
            $addr = $dom->createElement('Адрес');
            $this->appendAddressChoice($dom, $addr, $p->address);
            $el->appendChild($addr);
        }

        $kont = $dom->createElement('Конт');
        if ($p->contact !== null) {
            if (!empty($p->contact->phones)) {
                foreach ($p->contact->phones as $phone) {
                    $this->append($dom, $kont, 'Тлф', $phone);
                }
            } elseif ($p->contact->phone !== null) {
                $this->append($dom, $kont, 'Тлф', $p->contact->phone);
            }
            if (!empty($p->contact->emails)) {
                foreach ($p->contact->emails as $email) {
                    $this->append($dom, $kont, 'ЭлПочта', $email);
                }
            } elseif ($p->contact->email !== null) {
                $this->append($dom, $kont, 'ЭлПочта', $p->contact->email);
            }
            $this->append($dom, $kont, 'ИнКонт', $p->contact->other);
        }
        $el->appendChild($kont);

        $parent->appendChild($el);
    }

    /** ИдСв (choice: СвЮЛУч | СвИП | СвИнНеУч | СвФЛУчаст). */
    protected function appendIdSv(DOMDocument $dom, DOMElement $parent, Participant $p): void
    {
        $idSv = $dom->createElement('ИдСв');

        if ($p->legalEntity !== null) {
            $ul = $p->legalEntity;
            $svUl = $dom->createElement('СвЮЛУч');
            $svUl->setAttribute('НаимОрг', $ul->name ?? '');
            $svUl->setAttribute('ИННЮЛ', $ul->inn ?? '');
            $svUl->setAttribute('КПП', $ul->kpp ?? '');
            $idSv->appendChild($svUl);
        } elseif ($p->ip !== null) {
            $ip = $p->ip;
            $svIp = $dom->createElement('СвИП');
            $svIp->setAttribute('ИННФЛ', $ip->inn ?? '');
            $this->setAttributeIf($svIp, 'ОГРНИП', $ip->ogrnip);
            $this->setAttributeIf($svIp, 'ИныеСвед', $ip->other);
            $this->appendFio($dom, $svIp, $ip->lastName, $ip->firstName, $ip->middleName);
            $idSv->appendChild($svIp);
        } elseif ($p->individual !== null) {
            $ind = $p->individual;
            $svFl = $dom->createElement('СвФЛУчаст');
            $svFl->setAttribute('ИННФЛ', $ind->inn ?? '');
            $this->setAttributeIf($svFl, 'ИныеСвед', $ind->other);
            $this->appendFio($dom, $svFl, $ind->lastName, $ind->firstName, $ind->middleName);
            $idSv->appendChild($svFl);
        } elseif ($p->foreign !== null) {
            $f = $p->foreign;
            $svIn = $dom->createElement('СвИнНеУч');
            $this->setAttributeIf($svIn, 'ИдСтат', $f->idStat);
            $this->setAttributeIf($svIn, 'Стран', $f->countryCode);
            $this->setAttributeIf($svIn, 'Наим', $f->name);
            $this->setAttributeIf($svIn, 'Идентиф', $f->identification);
            $idSv->appendChild($svIn);
        }

        $parent->appendChild($idSv);
    }

    /**
     * АдресТип (choice): АдрРФ (если задан regionCode или index) либо АдрИнф.
     */
    protected function appendAddressChoice(DOMDocument $dom, DOMElement $parent, Address $addr): void
    {
        if (!empty($addr->regionCode) || !empty($addr->index)) {
            $rf = $dom->createElement('АдрРФ');
            $rf->setAttribute('Индекс', (string)($addr->index ?? ''));
            $rf->setAttribute('КодРегион', (string)($addr->regionCode ?? ''));
            $this->setAttributeIf($rf, 'Район', $addr->district);
            $this->setAttributeIf($rf, 'Город', $addr->city);
            $this->setAttributeIf($rf, 'НаселПункт', $addr->settlement);
            $this->setAttributeIf($rf, 'Улица', $addr->street);
            $this->setAttributeIf($rf, 'Дом', $addr->house);
            $this->setAttributeIf($rf, 'Корпус', $addr->building);
            $this->setAttributeIf($rf, 'Кварт', $addr->flat);
            $parent->appendChild($rf);
        } else {
            $inf = $dom->createElement('АдрИнф');
            $this->setAttributeIf($inf, 'КодСтр', $addr->countryCode);
            $inf->setAttribute('АдрТекст', (string)($addr->full ?? ''));
            $parent->appendChild($inf);
        }
    }

    /**
     * АдресПользТип: <$name @АдрКоммент?>, дочерний <Адрес> с АдрРФ/АдрИнф.
     */
    protected function appendUserAddress(DOMDocument $dom, DOMElement $parent, string $name, Address $addr): void
    {
        $el = $dom->createElement($name);
        $this->setAttributeIf($el, 'АдрКоммент', $addr->full);
        $inner = $dom->createElement('Адрес');
        $this->appendAddressChoice($dom, $inner, $addr);
        $el->appendChild($inner);
        $parent->appendChild($el);
    }

    protected function appendRequisites(DOMDocument $dom, DOMElement $parent, string $name, DocumentRequisites $doc): void
    {
        $el = $dom->createElement($name);
        $this->setAttributeIf($el, 'НаимДок', $doc->name);
        $this->setAttributeIf($el, 'НомерДок', $doc->number);
        if ($doc->date !== null) {
            $el->setAttribute('ДатаДок', $doc->date->format('d.m.Y'));
        }
        foreach ($doc->legalParticipants as $participant) {
            $this->appendIdRekSost($dom, $el, (string)$participant);
        }
        $parent->appendChild($el);
    }

    protected function appendShipperPerson(DOMDocument $dom, DOMElement $parent, Person $person): void
    {
        // СвЛицГО: @Полн (1..3) + РеквДокПолн + РеквЛицГО (УчастникТип).
        $el = $dom->createElement('СвЛицГО');
        $el->setAttribute('Полн', $person->authority ?? '1');
        if ($person->authorityDocument !== null) {
            $this->appendRequisites($dom, $el, 'РеквДокПолн', $person->authorityDocument);
        }
        if ($person->participant !== null) {
            $this->appendParticipant($dom, $el, 'РеквЛицГО', $person->participant);
        }
        $parent->appendChild($el);
    }

    protected function appendSupplyPoint(DOMDocument $dom, DOMElement $parent, SupplyPoint $point): void
    {
        $el = $dom->createElement('ПунктПод');
        if ($point->dateTime !== null) {
            $el->setAttribute('ДатВрПод', $this->formatDateTimeVZ($point->dateTime, $point->hasTimezone));
        }
        $el->setAttribute('НалКоорТочВрПод', $point->hasTimezone ? '1' : '0');
        if ($point->deadlineTime !== null) {
            $el->setAttribute('ПредВрПод', $point->deadlineTime);
            $el->setAttribute('НалКоорТочПредВрПод', ($point->deadlineHasTimezone ?? false) ? '1' : '0');
        }
        if ($point->address !== null) {
            $this->appendUserAddress($dom, $el, 'АдрПунктПод', $point->address);
        }
        $parent->appendChild($el);
    }

    protected function appendRoutePoint(DOMDocument $dom, DOMElement $parent, RoutePoint $point): void
    {
        $el = $dom->createElement('АдрПункт');
        $el->setAttribute('Опер', $point->operation);
        if ($point->number !== null) {
            $el->setAttribute('ПорНомПункт', (string)$point->number);
        }
        if ($point->dateTime !== null) {
            $el->setAttribute('ДатВрОпер', $this->formatDateTimeVZ($point->dateTime, (bool)$point->hasTimezone));
            $el->setAttribute('НалКоорТочВрОпер', ($point->hasTimezone ?? false) ? '1' : '0');
        }
        if ($point->deadlineTime !== null) {
            $el->setAttribute('ПредВрОпер', $point->deadlineTime);
            $el->setAttribute('НалКоорТочПредВрОпер', ($point->deadlineHasTimezone ?? false) ? '1' : '0');
        }
        if ($point->address !== null) {
            $this->appendUserAddress($dom, $el, 'АдресПункт', $point->address);
        }
        if ($point->infrastructureOwnerName !== null || $point->infrastructureOwnerInn !== null) {
            $owner = $dom->createElement('ОргВладИнфр');
            $owner->setAttribute('НаимВладИнфр', (string)($point->infrastructureOwnerName ?? ''));
            $owner->setAttribute('ИННВладИнфр', (string)($point->infrastructureOwnerInn ?? ''));
            $el->appendChild($owner);
        }
        $parent->appendChild($el);
    }

    protected function appendCargo(DOMDocument $dom, DOMElement $parent, OrderCargo $cargo): void
    {
        $el = $dom->createElement('ОпГруз');
        $el->setAttribute('НаимГруз', $cargo->name);
        $el->setAttribute('СостГруз', $cargo->condition);
        $el->setAttribute('ВидТар', $cargo->packagingType);
        $this->setAttributeIf($el, 'ДрОГруз', $cargo->otherInfo);
        $el->setAttribute('КолГрМест', (string)$cargo->units);
        $el->setAttribute('МетОпрМасс', $cargo->weightMethod);
        $el->setAttribute('Объем', $this->formatDecimal($cargo->volume, 2));
        $el->setAttribute('РаспрГр', $cargo->distributable ? '1' : '0');
        $el->setAttribute('ДелГр', $cargo->divisible ? '1' : '0');
        if ($cargo->driverParticipatesLoading !== null) {
            $el->setAttribute('УчВодПогрГр', $cargo->driverParticipatesLoading ? '1' : '0');
        }
        if ($cargo->containerCargo !== null) {
            $el->setAttribute('ПрГрКонт', $cargo->containerCargo ? '1' : '0');
        }

        // Дочерние в порядке XSD: МасГруз, РазмерГрМест, ИнфОпасн?, ЗакУсл?, ДогУслПрвз?, Пункт*, ИнфПол*
        $mas = $dom->createElement('МасГруз');
        if ($cargo->netWeight !== null) {
            $mas->setAttribute('МасНетЗнач', $this->formatDecimal($cargo->netWeight, 2));
        }
        $mas->setAttribute('МасБрутЗнач', $this->formatDecimal($cargo->grossWeight, 2));
        if ($cargo->pallets !== null) {
            $mas->setAttribute('КолПалл', (string)$cargo->pallets);
        }
        if ($cargo->tareWeight !== null) {
            $mas->setAttribute('МасТар', $this->formatDecimal($cargo->tareWeight, 2));
        }
        $el->appendChild($mas);

        $razm = $dom->createElement('РазмерГрМест');
        $razm->setAttribute('ВысЗнач', $this->formatDecimal($cargo->height, 3));
        $razm->setAttribute('ДлЗнач', $this->formatDecimal($cargo->length, 3));
        $razm->setAttribute('ШирЗнач', $this->formatDecimal($cargo->width, 3));
        $el->appendChild($razm);

        if ($cargo->dangerous !== null) {
            $this->appendDangerous($dom, $el, $cargo->dangerous);
        }

        $parent->appendChild($el);
    }

    protected function appendDangerous(DOMDocument $dom, DOMElement $parent, DangerousCargoInfo $d): void
    {
        // Минимальный набор для первой версии; полный ДОПОГ — расширение без ломки API.
        $el = $dom->createElement('ИнфОпасн');
        $this->setAttributeIf($el, 'НомООН', $d->unNumber);
        $this->setAttributeIf($el, 'НаимОтгр', $d->properShippingName);
        $this->setAttributeIf($el, 'Класс', $d->class);
        $this->setAttributeIf($el, 'ГрУпак', $d->packingGroup);
        $this->setAttributeIf($el, 'КодОгрТун', $d->tunnelRestrictionCode);
        $parent->appendChild($el);
    }

    protected function appendVehicleRequirements(DOMDocument $dom, DOMElement $parent, VehicleRequirements $r): void
    {
        $el = $dom->createElement('ПарТСПрвз');
        $el->setAttribute('Тип', $r->type);
        $el->setAttribute('Грузопод', $this->formatDecimal($r->carryingCapacity, 2));
        $el->setAttribute('Вместим', $this->formatDecimal($r->capacity, 2));
        $this->setAttributeIf($el, 'ТипПогр', $r->loadingType);
        $this->setAttributeIf($el, 'ТипКуз', $r->bodyType);
        $this->setAttributeIf($el, 'ДрПарам', $r->otherParams);
        $parent->appendChild($el);
    }

    protected function appendTemperature(DOMDocument $dom, DOMElement $parent, TemperatureRegime $t): void
    {
        $el = $dom->createElement('ТемпРеж');
        if ($t->min !== null) $el->setAttribute('ТемпНеНиже', $this->formatDecimal($t->min, 2));
        if ($t->max !== null) $el->setAttribute('ТемпНеВыше', $this->formatDecimal($t->max, 2));
        $parent->appendChild($el);
    }

    protected function appendHumidity(DOMDocument $dom, DOMElement $parent, HumidityRegime $h): void
    {
        $el = $dom->createElement('ВлажнРеж');
        if ($h->min !== null) $el->setAttribute('ВлажнНеНиже', $this->formatDecimal($h->min, 2));
        if ($h->max !== null) $el->setAttribute('ВлажнНеВыше', $this->formatDecimal($h->max, 2));
        $parent->appendChild($el);
    }

    protected function appendInfoField(DOMDocument $dom, DOMElement $parent, InfoField $field): void
    {
        $el = $dom->createElement('ИнфПол');
        $el->setAttribute('ИдФайлИнфПол', $field->fileId);
        $el->setAttribute('ТекстИнф', $field->text);
        $parent->appendChild($el);
    }

    /**
     * Подписант ЭЗЗ. Атрибуты: Должн, ТипПодпис?, СпосПодтПолном, ДопСведПодп?; дочерние: ФИО,
     * СвДоверЭл* и СвДоверБум* (по XSD ПодписантТип).
     */
    protected function appendOrderSignatory(DOMDocument $dom, DOMElement $parent, string $name, OrderSignatory $s): void
    {
        $el = $dom->createElement($name);
        $el->setAttribute('Должн', $s->position);
        $this->setAttributeIf($el, 'ТипПодпис', $s->signatureType);
        $el->setAttribute('СпосПодтПолном', $s->authorityConfirmation);
        $this->setAttributeIf($el, 'ДопСведПодп', $s->additionalInfo);
        $this->appendFio($dom, $el, $s->lastName, $s->firstName, $s->middleName);
        foreach ($s->electronicPoa as $poa) {
            $poaEl = $dom->createElement('СвДоверЭл');
            $this->setAttributeIf($poaEl, 'НомДовер', $poa->number);
            if ($poa->date !== null) $poaEl->setAttribute('ДатаДовер', $poa->date->format('d.m.Y'));
            $this->setAttributeIf($poaEl, 'ИННДовер', $poa->delegatorInn);
            $el->appendChild($poaEl);
        }
        foreach ($s->paperPoa as $poa) {
            $poaEl = $dom->createElement('СвДоверБум');
            $this->setAttributeIf($poaEl, 'ВнНомДовер', $poa->number);
            if ($poa->date !== null) $poaEl->setAttribute('ДатаДовер', $poa->date->format('d.m.Y'));
            $this->setAttributeIf($poaEl, 'СвИдДовер', $poa->issuedBy);
            $el->appendChild($poaEl);
        }
        $parent->appendChild($el);
    }

    /** Формат числа с фиксированной точностью (точка-разделитель, без разделителей тысяч). */
    protected function formatDecimal(float $value, int $scale): string
    {
        return number_format($value, $scale, '.', '');
    }

    /**
     * ДатаВремяВЗТип по XSD ЭЗЗ — фиксированная длина 25 символов: 'ДД.ММ.ГГГГTЧЧ:ММ:СС±ЧЧ:ММ'.
     * Формат не зависит от флага NalKoorToch (тот управляет смыслом, но не представлением).
     */
    protected function formatDateTimeVZ(DateTimeInterface $dt, bool $hasTimezone): string
    {
        return $dt->format('d.m.Y\TH:i:sP');
    }
}
