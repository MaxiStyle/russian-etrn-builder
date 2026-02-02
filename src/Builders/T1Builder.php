<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMException;
use MaxiStyle\EtrnBuilder\Entities;
use DOMDocument;
use DOMElement;

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

        $document = $dom->createElement('Документ');

        $document->setAttribute('КНД', $doc->knd);
        $document->setAttribute('ПоФактХЖ', $doc->docName);
        $document->setAttribute('ДатИнфГО', $doc->dateFile->format('d.m.Y'));
        $document->setAttribute('ВрИнфГО',$doc->dateFile->format('H:i:s'));

        // Содержание транспортной накладной, информация грузоотправителя
        $shipment = $doc->shipment;
        $shipper = $shipment->shipper;
        
        $sodInfGo = $dom->createElement('СодИнфГО');
        $sodInfGo->setAttribute('УИД_ТрН', $doc->documentId ?? ''); // UUID документа
        $sodInfGo->setAttribute('СодОпер', $shipment->operation);
        $sodInfGo->setAttribute('НомерТрН', $doc->numTrN ?? '');
        $sodInfGo->setAttribute('ДатаТрН', $doc->dateTrN?->format('d.m.Y') ?? '');
        $sodInfGo->setAttribute('НомЗак', $doc->orderNumber ?? '');
        $sodInfGo->setAttribute('ДатаЗак', $doc->orderDate?->format('d.m.Y') ?? '');

        // Грузоотправитель
        $svGo = $dom->createElement('СвГО');
        $svGo->setAttribute('ГОЭксп', $shipper->status ?? '0'); // Статус грузоотправителя (грузоотправитель является / не является экспедитором)

        $rekIdentGo = $dom->createElement('РекИдентГО');
            $idSv = $dom->createElement('ИдСв');
                if ($shipper->legalEntity !== null) {
                    $ul = $shipper->legalEntity;
                    $svYuLuch = $dom->createElement('СвЮЛУч');
                    $svYuLuch->setAttribute('НаимОрг', $ul->name);
                    $svYuLuch->setAttribute('ИННЮЛ', $ul->inn);
                    $svYuLuch->setAttribute('КПП', $ul->kpp);
                    $idSv->appendChild($svYuLuch);
                } else if ($shipper->ip !== null) {
                    $ip = $shipper->ip;
                    $svIp = $dom->createElement('СвИП');
                    $svIp->setAttribute('ФИО', $ip->fio);
                    $svIp->setAttribute('ИННФЛ', $ip->inn);
                    $svIp->setAttribute('ОГРНИП', $ip->ogrnip);
                    $svIp->setAttribute('ИныеСвед', $ip->other);
                    $idSv->appendChild($svIp);
                }
            $rekIdentGo->appendChild($idSv);

            $addr = $dom->createElement('Адрес');
            $address = $shipper->address;
            $addressElement = $dom->createElement('АдрРФ');
            $addressElement->setAttribute('Индекс', $address->index ?? '');
            $addressElement->setAttribute('КодРегион', $address->regionCode ?? '');
            $addressElement->setAttribute('Район', $address->district ?? '');
            $addressElement->setAttribute('Город', $address->city ?? '');
            $addressElement->setAttribute('НаселПункт', $address->settlement ?? '');
            $addressElement->setAttribute('Улица', $address->street ?? '');
            $addressElement->setAttribute('Дом', $address->house ?? '');
            $addressElement->setAttribute('Корпус', $address->building ?? '');
            $addressElement->setAttribute('Кварт', $address->flat ?? '');
            $addressElement->setAttribute('КодСтр', $address->countryCode ?? '');
            $addressElement->setAttribute('АдрТекст', $address->full ?? '');
            $addr->appendChild($addressElement);
            $rekIdentGo->appendChild($addr);

            $contact = $shipper->contact;
            $contactElement = $dom->createElement('Контакт');
            $this->append($dom, $contactElement, 'Тлф', $contact->phone);
            $this->append($dom, $contactElement, 'ЭлПочта', $contact->email);
            $this->append($dom, $contactElement, 'ИнКонт', $contact->other);
            $rekIdentGo->appendChild($contactElement);


        $svGo->appendChild($rekIdentGo);
        $sodInfGo->appendChild($svGo);

        // ГРУЗОПОЛУЧАТЕЛЬ
        if ($doc->consignee !== null) {
            $consignee = $doc->consignee;

            $svGp = $dom->createElement('СвГП');
            $rekIdentGp = $dom->createElement('РекИдентГП');

            $idSv = $dom->createElement('ИдСв');
            if ($consignee->legalEntity !== null) {
                $ul = $consignee->legalEntity;
                $svYuLuch = $dom->createElement('СвЮЛУч');
                $svYuLuch->setAttribute('НаимОрг', $ul->name);
                $svYuLuch->setAttribute('ИННЮЛ', $ul->inn);
                $svYuLuch->setAttribute('КПП', $ul->kpp);
                $idSv->appendChild($svYuLuch);
            } else if ($consignee->ip !== null) {
                $ip = $consignee->ip;
                $svIp = $dom->createElement('СвИП');
                $svIp->setAttribute('ФИО', $ip->fio);
                $svIp->setAttribute('ИННФЛ', $ip->inn);
                $svIp->setAttribute('ОГРНИП', $ip->ogrnip);
                $svIp->setAttribute('ИныеСвед', $ip->other);
                $idSv->appendChild($svIp);
            }
            $rekIdentGp->appendChild($idSv);

            $addr = $dom->createElement('Адрес');
            $address = $consignee->address;
            $addressElement = $dom->createElement('АдрРФ');
            $addressElement->setAttribute('Индекс', $address->index ?? '');
            $addressElement->setAttribute('КодРегион', $address->regionCode ?? '');
            $addressElement->setAttribute('Район', $address->district ?? '');
            $addressElement->setAttribute('Город', $address->city ?? '');
            $addressElement->setAttribute('НаселПункт', $address->settlement ?? '');
            $addressElement->setAttribute('Улица', $address->street ?? '');
            $addressElement->setAttribute('Дом', $address->house ?? '');
            $addressElement->setAttribute('Корпус', $address->building ?? '');
            $addressElement->setAttribute('Кварт', $address->flat ?? '');
            $addressElement->setAttribute('КодСтр', $address->countryCode ?? '');
            $addressElement->setAttribute('АдрТекст', $address->full ?? '');
            $addr->appendChild($addressElement);
            $rekIdentGp->appendChild($addr);

            $contact = $consignee->contact;
            if ($contact !== null) {
                $contactElement = $dom->createElement('Контакт');
                $this->append($dom, $contactElement, 'Тлф', $contact->phone);
                $this->append($dom, $contactElement, 'ЭлПочта', $contact->email);
                $this->append($dom, $contactElement, 'ИнКонт', $contact->other);
                $rekIdentGp->appendChild($contactElement);
            }

            $svGp->appendChild($rekIdentGp);

            // Адрес доставки груза
            if (isset($consignee->deliveryAddress)) {
                $adr = $dom->createElement('АдресДостГр');
                $deliveryAddress = $consignee->deliveryAddress;
                $adrRf = $dom->createElement('АдресИнф');
                $adrRf->setAttribute('КодСтр', $deliveryAddress->countryCode ?? '643');
                $adrRf->setAttribute('АдрТекст', $deliveryAddress->full ?? '');
                $adr->appendChild($adrRf);
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
            $gruz->setAttribute('СпУпак', $cargo->packagingMethod);
            $gruz->setAttribute('ВидТар', $cargo->packagingType);
            if (isset($cargo->cargoUnits)) {
                $gruz->setAttribute('КолМестГр', $cargo->cargoUnits);
            }
            $mark = $dom->createElement('Марк', $cargo->marking);
            $gruz->appendChild($mark);
            $weightElement = $dom->createElement('ПлМасГруз');
            if (isset($cargo->plannedWeight)) {
                $weightElement->setAttribute('МасБрутЗнач', $cargo->plannedWeight);
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
            $tel = $dom->createElement('Тлф', $conditions->contactInfo);
            $kont->appendChild($tel);
            $svPa->appendChild($kont);
            $ukazGo->appendChild($svPa);

            $sodInfGo->appendChild($ukazGo);
        }

        $document->appendChild($sodInfGo);

        // ПЕРЕВОЗЧИК
        if (isset($doc->carrier)) {
            $carrier = $doc->carrier;

            $svPer = $dom->createElement('СвПер');

            $idSv = $dom->createElement('ИдСв');
            if ($carrier->legalEntity !== null) {
                $ul = $carrier->legalEntity;
                $svYuLuch = $dom->createElement('СвЮЛУч');
                $svYuLuch->setAttribute('НаимОрг', $ul->name);
                $svYuLuch->setAttribute('ИННЮЛ', $ul->inn);
                $svYuLuch->setAttribute('КПП', $ul->kpp);
                $idSv->appendChild($svYuLuch);
            } else if ($carrier->ip !== null) {
                $ip = $carrier->ip;
                $svIp = $dom->createElement('СвИП');
                $svIp->setAttribute('ФИО', $ip->fio);
                $svIp->setAttribute('ИННФЛ', $ip->inn);
                $svIp->setAttribute('ОГРНИП', $ip->ogrnip);
                $svIp->setAttribute('ИныеСвед', $ip->other);
                $idSv->appendChild($svIp);
            }
            $svPer->appendChild($idSv);

            $addr = $dom->createElement('Адрес');
            $address = $carrier->address;
            $addressElement = $dom->createElement('АдрРФ');
            $addressElement->setAttribute('Индекс', $address->index ?? '');
            $addressElement->setAttribute('КодРегион', $address->regionCode ?? '');
            $addressElement->setAttribute('Район', $address->district ?? '');
            $addressElement->setAttribute('Город', $address->city ?? '');
            $addressElement->setAttribute('НаселПункт', $address->settlement ?? '');
            $addressElement->setAttribute('Улица', $address->street ?? '');
            $addressElement->setAttribute('Дом', $address->house ?? '');
            $addressElement->setAttribute('Корпус', $address->building ?? '');
            $addressElement->setAttribute('Кварт', $address->flat ?? '');
            $addressElement->setAttribute('КодСтр', $address->countryCode ?? '');
            $addressElement->setAttribute('АдрТекст', $address->full ?? '');
            $addr->appendChild($addressElement);
            $svPer->appendChild($addr);

            $contact = $carrier->contact;
            if ($contact !== null) {
                $contactElement = $dom->createElement('Контакт');
                $this->append($dom, $contactElement, 'Тлф', $contact->phone);
                $this->append($dom, $contactElement, 'ЭлПочта', $contact->email);
                $this->append($dom, $contactElement, 'ИнКонт', $contact->other);
                $svPer->appendChild($contactElement);
            }

            $sodInfGo->appendChild($svPer);
        }

        // ВОДИТЕЛЬ
        if ($doc->driver !== null) {
            $driver = $doc->driver;

            $voditel = $dom->createElement('СвВодит');
            if (isset($driver->licenseSeries)) {
                $voditel->setAttribute('СерВУ', $driver->licenseSeries);
            }
            if (isset($driver->licenseNumber)) {
                $voditel->setAttribute('НомВУ', $driver->licenseNumber);
            }
            if (isset($driver->licenseIssueDate)) {
                $voditel->setAttribute('ДатаВыдВУ', $driver->licenseIssueDate);
            }
            if (isset($driver->phone)) {
                $tel = $dom->createElement('Тлф', $driver->phone);
            }
            $voditel->appendChild($tel);
            $fio = $dom->createElement('ФИО');
            $fio->setAttribute('Фамилия', $driver->lastName);
            $fio->setAttribute('Имя', $driver->firstName);
            $fio->setAttribute('Отчество', $driver->middleName);
            $voditel->appendChild($fio);

            $sodInfGo->appendChild($voditel);
        }

        // ТРАНСПОРТНОЕ СРЕДСТВО
        if ($doc->vehicle !== null) {
            $vehicle = $doc->vehicle;

            $svTs = $dom->createElement('СвТС');
            $ts = $dom->createElement('ТС');
            $ts->setAttribute('РегНомер', $vehicle->regNumber);
            $ts->setAttribute('ТипВлад', (string)$vehicle->ownershipType);
            $parTs = $dom->createElement('ПарТС');
            if (isset($vehicle->type)) {
                $parTs->setAttribute('Тип', $vehicle->type);
            }
            if (isset($vehicle->brand)) {
                $parTs->setAttribute('Марка', $vehicle->brand);
            }
            if (isset($vehicle->carryingCapacity)) {
                $parTs->setAttribute('Грузопод', (string)$vehicle->carryingCapacity);
            }
            if (isset($vehicle->capacity)) {
                $parTs->setAttribute('Вместим', (string)$vehicle->capacity);
            }
            $ts->appendChild($parTs);
            $svTs->appendChild($ts);
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
                $loadingAddress = $route->loadingAddress;
                $adrRf = $dom->createElement('АдресИнф');
                $adrRf->setAttribute('КодСтр', $loadingAddress->countryCode ?? '643');
                $adrRf->setAttribute('АдрТекст', $loadingAddress->full ?? '');
                $adr->appendChild($adrRf);
                $svPogruz->appendChild($adr);
            }

            // Сведения о лице, осуществляющем погрузку груза в транспортное средство
            $lico = $dom->createElement('СвЛицПогрГр');
            $lico->setAttribute('СовпГОП', '1'); // Признак совпадения с грузоотправителем
            $identRekGo = $dom->createElement('ИдентРекГО');
            if ($shipper->legalEntity !== null) {
                $inn = $dom->createElement('ИННЮЛ', $shipper->legalEntity->inn);
            } else if ($shipper->ip !== null) {
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
            if ($shipper->legalEntity !== null) {
                $inn = $dom->createElement('ИННЮЛ', $shipper->legalEntity->inn);
            } else if ($shipper->ip !== null) {
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
            $signatory = $doc->signatory;

            $podpis = $dom->createElement('Подписант');
            $podpis->setAttribute('Подписант', $signatory->status);
            $fio = $dom->createElement('ФИО');
            $fio->setAttribute('Фамилия', $signatory->lastName);
            $fio->setAttribute('Имя', $signatory->firstName);
            $fio->setAttribute('Отчество', $signatory->middleName);
            $podpis->appendChild($fio);

            $document->appendChild($podpis);
        }

        $parent->appendChild($document);
    }
}
