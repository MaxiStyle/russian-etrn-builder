<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMDocument;
use DOMElement;
use DOMException;
use MaxiStyle\EtrnBuilder\Entities;

/**
 * Билдер Титул T1 - Информация о грузе, ТС, водителе
 */
class T1Builder extends DocumentBuilder implements DocumentBuilderInterface
{
    /**
     * @throws DOMException
     */
    public function build(DOMDocument $dom, DOMElement $parent, object $doc): void
    {
        if (!$doc instanceof Entities\T1) {
            throw new \InvalidArgumentException('Документ должен быть экземпляром T1');
        }

        // Атрибуты корневого элемента
        $parent->setAttribute('ИдФайл', $doc->fileId ?? ''); // имя файла
        $parent->setAttribute('ВерсПрог', $doc->softwareVersion ?? ''); // версия программы
        $parent->setAttribute('ВерсФорм', $doc->formatVersion ?? ''); // версия формата

        $document = $dom->createElement('Документ');

        $document->setAttribute('КНД', $doc->knd);
        $document->setAttribute('ПоФактХЖ', $doc->docName);
        $document->setAttribute('ДатИнфГО', $doc->dateFile->format('d.m.Y'));
        $document->setAttribute('ВрИнфГО', $doc->dateFile->format('H:i:s'));

        // Содержание транспортной накладной, информация грузоотправителя
        $shipment = $doc->shipment;
        $shipper = $doc->shipper;

        $sodInfGo = $dom->createElement('СодИнфГО');
        $sodInfGo->setAttribute('УИД_ТрН', $doc->documentUuid ?? ''); // UUID документа
        $sodInfGo->setAttribute('СодОпер', $shipment->operation);
        $sodInfGo->setAttribute('НомерТрН', $doc->transportNumber ?? '');
        $sodInfGo->setAttribute('ДатаТрН', $doc->transportDate?->format('d.m.Y') ?? '');
        $sodInfGo->setAttribute('НомЗак', $doc->orderNumber ?? '');
        $sodInfGo->setAttribute('ДатаЗак', $doc->orderDate?->format('d.m.Y') ?? '');

        // ИдЗак — идентификация файла обмена подписанного электронного заказа/заявки
        if ($doc->orderFileId !== null) {
            $this->appendReference(
                $dom, $sodInfGo, 'ИдЗак',
                'ИдФайлЗак', 'ДатФайлЗак', 'ВрФайлЗак',
                $doc->orderFileId,
                $doc->orderFileDate ?? '',
                $doc->orderFileTime ?? '',
                $doc->orderFileSignature ?? '',
            );
        }

        // Грузоотправитель
        $svGo = $dom->createElement('СвГО');
        $svGo->setAttribute('ГОЭксп', (string)($shipper->status ?? '0'));

        $rekIdentGo = $dom->createElement('РекИдентГО');
        $this->appendIdentity($dom, $rekIdentGo, $shipper->legalEntity ?? null, $shipper->ip ?? null);

        $addr = $dom->createElement('Адрес');
        $this->appendInlineAddressRf($dom, $addr, $shipper->address);
        $rekIdentGo->appendChild($addr);

        $this->appendContact($dom, $rekIdentGo, $shipper->contact ?? null);

        $svGo->appendChild($rekIdentGo);
        $sodInfGo->appendChild($svGo);

        if ($shipper->status) {
            // ЗАКАЗЧИК
            $customer = $doc->customer;
            $svKaz = $dom->createElement('СвЗак');

            $rekIdentZak = $dom->createElement('РекИдентЗак');
            $this->appendIdentity($dom, $rekIdentZak, $customer->legalEntity ?? null, $customer->ip ?? null);

            $addr = $dom->createElement('Адрес');
            $this->appendInlineAddressRf($dom, $addr, $customer->address);
            $rekIdentZak->appendChild($addr);

            $this->appendContact($dom, $rekIdentZak, $customer->contact ?? null);
            $svKaz->appendChild($rekIdentZak);

            $contract = $customer->contract;
            if ($contract) {
                $dogUslPer = $dom->createElement('ДогУслПер');
                $dogUslPer->setAttribute('НаимДок', $contract->name ?? '');
                $dogUslPer->setAttribute('НомерДок', $contract->number ?? '');
                $dogUslPer->setAttribute('ДатаДок', $contract?->date?->format('d.m.Y') ?? '');

                foreach ($contract->legalParticipants as $participant) {
                    $this->appendIdRekSost($dom, $dogUslPer, (string)$participant);
                }
                $svKaz->appendChild($dogUslPer);
            }

            $sodInfGo->appendChild($svKaz);
        }

        // ГРУЗОПОЛУЧАТЕЛЬ
        if ($doc->consignee !== null) {
            $consignee = $doc->consignee;

            $svGp = $dom->createElement('СвГП');
            $rekIdentGp = $dom->createElement('РекИдентГП');

            $this->appendIdentity($dom, $rekIdentGp, $consignee->legalEntity ?? null, $consignee->ip ?? null);

            $addr = $dom->createElement('Адрес');
            $this->appendInlineAddressRf($dom, $addr, $consignee->address);
            $rekIdentGp->appendChild($addr);

            $this->appendContact($dom, $rekIdentGp, $consignee->contact ?? null);

            $svGp->appendChild($rekIdentGp);

            // Адрес доставки груза
            if (isset($consignee->deliveryAddress)) {
                $adr = $dom->createElement('АдресДостГр');
                $this->appendAddressRf($dom, $adr, $consignee->deliveryAddress);
                $svGp->appendChild($adr);
            }

            $sodInfGo->appendChild($svGp);
        }

        // ГРУЗ
        if ($doc->cargo !== null) {
            $cargo = $doc->cargo;

            $svGruz = $dom->createElement('СвГруз');
            $gruz = $dom->createElement('ОпГруз');
            $gruz->setAttribute('НаимГруз', $cargo->name);
            $gruz->setAttribute('СостГруз', $cargo->condition);
            $gruz->setAttribute('СпУпак', $cargo->packagingMethod ?: '-');
            $gruz->setAttribute('ВидТар', $cargo->packagingType ?: '-');
            if (($cargo->otherInfo ?? '') !== '') {
                $gruz->setAttribute('ДрОГруз', $cargo->otherInfo);
            }
            if (isset($cargo->cargoUnits)) {
                $gruz->setAttribute('КолМестГр', (string)$cargo->cargoUnits);
            }
            $mark = $dom->createElement('Марк', $cargo->marking);
            $gruz->appendChild($mark);

            // Габариты груза (ГабарТип) — опциональный блок; XSD требует все 3 атрибута,
            // если блок присутствует. Поэтому выводим только когда задано хотя бы одно значение;
            // недостающие заполняем нулями, чтобы не нарушить XSD.
            if ($cargo->height > 0 || $cargo->length > 0 || $cargo->width > 0) {
                $gabar = $dom->createElement('Габар');
                $gabar->setAttribute('ВысЗнач', (string)$cargo->height);
                $gabar->setAttribute('ДлЗнач', (string)$cargo->length);
                $gabar->setAttribute('ШирЗнач', (string)$cargo->width);
                $gruz->appendChild($gabar);
            }

            $weightElement = $dom->createElement('ПлМасГруз');
            if (isset($cargo->plannedWeight)) {
                $weightElement->setAttribute('МасБрутЗнач', (string)$cargo->plannedWeight);
            }
            $gruz->appendChild($weightElement);

            $svGruz->appendChild($gruz);
            $sodInfGo->appendChild($svGruz);
        }

        // СВЕДЕНИЯ ОБ УКАЗАНИЯХ ГРУЗООТПРАВИТЕЛЯ ПО ОСОБЫМ УСЛОВИЯМ ПЕРЕВОЗКИ
        if ($doc->conditions !== null) {
            $conditions = $doc->conditions;

            $ukazGo = $dom->createElement('УказГО');
            $ukazGo->setAttribute('ЗапрПерегруз', $conditions->noTransshipment);
            $ukazGo->setAttribute('ДатВрДостГр', $conditions->deliveryDateTime);
            $ukazGo->setAttribute('НалКоорТочВрДост', '1'); // Применение координации точного времени (UTC)
            $ukazGo->setAttribute('УкНормПрвз', 'Отсутствует'); // Указания в отношении выполнения норм перевозки
            $svPa = $dom->createElement('СвПА');
            $svPa->setAttribute('ЛицоПА', $conditions->redirectionPerson);
            $svPa->setAttribute('СпосПерУкПА', $conditions->redirectionMethod);
            $kont = $dom->createElement('КонтПА');
            $this->append($dom, $kont, 'Тлф', $conditions->contactInfo);
            $svPa->appendChild($kont);
            $ukazGo->appendChild($svPa);

            $sodInfGo->appendChild($ukazGo);
        }

        $document->appendChild($sodInfGo);

        // ПЕРЕВОЗЧИК
        if (isset($doc->carrier)) {
            $carrier = $doc->carrier;

            $svPer = $dom->createElement('СвПер');

            $this->appendIdentity($dom, $svPer, $carrier->legalEntity ?? null, $carrier->ip ?? null);

            $addr = $dom->createElement('Адрес');
            $this->appendInlineAddressRf($dom, $addr, $carrier->address);
            $svPer->appendChild($addr);

            $this->appendContact($dom, $svPer, $carrier->contact ?? null);

            $sodInfGo->appendChild($svPer);
        }

        // ВОДИТЕЛИ (один или несколько СвВодит)
        foreach ($doc->drivers as $driver) {
            $voditel = $dom->createElement('СвВодит');
            if (isset($driver->inn) && $driver->inn) {
                $voditel->setAttribute('ИННФЛ', $driver->inn);
            }
            if (isset($driver->licenseSeries) && $driver->licenseSeries) {
                $voditel->setAttribute('СерВУ', $driver->licenseSeries);
            }
            if (isset($driver->licenseNumber) && $driver->licenseNumber) {
                $voditel->setAttribute('НомВУ', $driver->licenseNumber);
            }
            if (isset($driver->licenseIssueDate) && $driver->licenseIssueDate) {
                $voditel->setAttribute('ДатаВыдВУ', $driver->licenseIssueDate);
            }
            if (isset($driver->phone) && $driver->phone) {
                $this->append($dom, $voditel, 'Тлф', $driver->phone);
            }
            $this->appendFio(
                $dom, $voditel,
                $driver->lastName ?? null,
                $driver->firstName ?? null,
                !empty($driver->middleName) ? $driver->middleName : null,
            );

            $sodInfGo->appendChild($voditel);
        }

        // ТРАНСПОРТНОЕ СРЕДСТВО (тягач + прицепы)
        if ($doc->vehicle !== null || !empty($doc->trailers)) {
            $svTs = $dom->createElement('СвТС');

            if ($doc->vehicle !== null) {
                $vehicle = $doc->vehicle;
                $ts = $dom->createElement('ТС');
                $ts->setAttribute('РегНомер', $vehicle->regNumber);
                $ts->setAttribute('ТипВлад', (string)$vehicle->ownershipType);
                $parTs = $dom->createElement('ПарТС');
                if (isset($vehicle->type)) $parTs->setAttribute('Тип', $vehicle->type);
                if (isset($vehicle->brand)) $parTs->setAttribute('Марка', $vehicle->brand);
                if (isset($vehicle->carryingCapacity)) $parTs->setAttribute('Грузопод', (string)$vehicle->carryingCapacity);
                if (isset($vehicle->capacity)) $parTs->setAttribute('Вместим', (string)$vehicle->capacity);
                $ts->appendChild($parTs);
                $this->appendRentLeaseDocument($dom, $ts, $vehicle->rentLeaseDocument ?? null);
                $svTs->appendChild($ts);
            }

            foreach ($doc->trailers as $trailer) {
                $pritsep = $dom->createElement('Прицеп');
                $pritsep->setAttribute('РегНомер', $trailer->regNumber);
                $pritsep->setAttribute('ТипВлад', (string)$trailer->ownershipType);
                if ($trailer->stsNumber !== null) $pritsep->setAttribute('НомСТС', $trailer->stsNumber);
                if ($trailer->vin !== null) $pritsep->setAttribute('НомерВИН', $trailer->vin);
                $parTs = $dom->createElement('ПарТС');
                if (isset($trailer->type)) $parTs->setAttribute('Тип', $trailer->type);
                if (isset($trailer->brand)) $parTs->setAttribute('Марка', $trailer->brand);
                if (isset($trailer->carryingCapacity)) $parTs->setAttribute('Грузопод', (string)$trailer->carryingCapacity);
                if (isset($trailer->capacity)) $parTs->setAttribute('Вместим', (string)$trailer->capacity);
                $pritsep->appendChild($parTs);
                $this->appendRentLeaseDocument($dom, $pritsep, $trailer->rentLeaseDocument ?? null);
                $svTs->appendChild($pritsep);
            }

            $sodInfGo->appendChild($svTs);
        }

        // СВЕДЕНИЯ ГРУЗООТПРАВИТЕЛЯ О ПЕРЕДАЧЕ ГРУЗА ПРИ ПРИЕМЕ ГРУЗА ПЕРЕВОЗЧИКОМ
        if ($doc->route !== null) {
            $route = $doc->route;

            $svPogruz = $dom->createElement('СвПогруз');
            $svPogruz->setAttribute('ЗаявПогр', $route->plannedArrival);
            $svPogruz->setAttribute('НалКоорТочВрЗаяв', '1'); // Применение координации точного времени (UTC)
            $svPogruz->setAttribute('ФДатВрПриб', $route->actualArrival);
            $svPogruz->setAttribute('НалКоорТочВрФПогр', '1'); // Применение координации точного времени (UTC)
            $svPogruz->setAttribute('ФДатВрУбыт', $route->actualDeparture);
            $svPogruz->setAttribute('НалКоорТочВрФУбыт', '1'); // Применение координации точного времени (UTC)
            $svPogruz->setAttribute('МасБрутОтгр', (string)$route->grossWeight);
            $svPogruz->setAttribute('МетОпрМасс', $route->weightDeterminationMethod);
            $svPogruz->setAttribute('КолМестПрием', (string)$route->cargoUnitsReceived);

            // Адрес места погрузки
            if ($route->loadingAddress !== null) {
                $adr = $dom->createElement('ФАдресПогр');
                $this->appendAddressRf($dom, $adr, $route->loadingAddress);
                $svPogruz->appendChild($adr);
            }

            // Сведения о лице, осуществляющем погрузку груза в транспортное средство
            $lico = $dom->createElement('СвЛицПогрГр');
            $lico->setAttribute('СовпГОП', '1'); // Признак совпадения с грузоотправителем
            $identRekGo = $dom->createElement('ИдентРекГО');
            if (isset($shipper->legalEntity)) {
                $inn = $dom->createElement('ИННЮЛ', $shipper->legalEntity->inn);
            } else if (isset($shipper->ip)) {
                $inn = $dom->createElement('ИННФЛ', $shipper->ip->inn);
            } else {
                $inn = $dom->createElement('ИННЮЛ', '');
            }
            $identRekGo->appendChild($inn);
            $lico->appendChild($identRekGo);
            $svPogruz->appendChild($lico);

            // Сведения о владельце объекта инфраструктуры пункта погрузки
            $vlad = $dom->createElement('ВладИнфр');
            $vlad->setAttribute('СовпГОВ', '1'); // Признак совпадения с грузоотправителем
            $identRekGo = $dom->createElement('ИдентРекГО');
            if (isset($shipper->legalEntity)) {
                $inn = $dom->createElement('ИННЮЛ', $shipper->legalEntity->inn);
            } else if (isset($shipper->ip)) {
                $inn = $dom->createElement('ИННФЛ', $shipper->ip->inn);
            } else {
                $inn = $dom->createElement('ИННЮЛ', '');
            }
            $identRekGo->appendChild($inn);
            $vlad->appendChild($identRekGo);
            $svPogruz->appendChild($vlad);

            $sodInfGo->appendChild($svPogruz);
        }

        // ПОДПИСАНТ
        if ($doc->signatory !== null) {
            $this->appendSignatory($dom, $document, $doc->signatory);
        }

        // ИдПолИной — идентификатор иного получателя (FNS ID перевозчика), добавляется перед Документ
        if (!empty($doc->recipientId)) {
            $idPolInoi = $dom->createElement('ИдПолИной', $doc->recipientId);
            $parent->appendChild($idPolInoi);
        }

        $parent->appendChild($document);
    }
}