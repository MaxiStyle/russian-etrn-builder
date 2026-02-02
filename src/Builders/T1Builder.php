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
        $shipperInfo = $doc->shipperInfo;
        $shipper = $shipperInfo->shipper;
        
        $SodInfGO = $dom->createElement('СодИнфГО');
        $SodInfGO->setAttribute('СодОпер', $shipperInfo->operation);
        $SodInfGO->setAttribute('НомерТрН', $doc->numTrN);
        $SodInfGO->setAttribute('ДатаТрН', $doc->dateTrN->format('d.m.Y'));
        $SodInfGO->setAttribute('НомЗак', $doc->numOrder);
        $SodInfGO->setAttribute('ДатаЗак', $doc->dateOrder->format('d.m.Y'));
        $SodInfGO->setAttribute('ГОЭксп', $shipper->goEksp);

        // Грузоотправитель
        $RekIdentGO = $dom->createElement('РекИдентГО');
            $IdSv = $dom->createElement('ИдСв');
                if ($shipper->ul !== null) {
                    $ul = $shipper->ul;
                    $SvYuLUch = $dom->createElement('СвЮЛУч');
                    $SvYuLUch->setAttribute('НаимОрг', $ul->name);
                    $SvYuLUch->setAttribute('ИННЮЛ', $ul->inn);
                    $SvYuLUch->setAttribute('КПП', $ul->kpp);
                    $IdSv->appendChild($SvYuLUch);
                } else if ($shipper->ip !== null) {
                    $ip = $shipper->ip;
                    $SvIP = $dom->createElement('СвИП');
                    $SvIP->setAttribute('ФИО', $ip->fio);
                    $SvIP->setAttribute('ИННФЛ', $ip->inn);
                    $SvIP->setAttribute('ОГРНИП', $ip->ogrnip);
                    $SvIP->setAttribute('ИныеСвед', $ip->other);
                    $IdSv->appendChild($SvIP);
                }
            $RekIdentGO->appendChild($IdSv);

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
            $RekIdentGO->appendChild($addr);

            $contact = $shipper->contact;
            $contactElement = $dom->createElement('Контакт');
            $this->append($dom, $contactElement, 'Тлф', $contact->phone);
            $this->append($dom, $contactElement, 'ЭлПочта', $contact->email);
            $this->append($dom, $contactElement, 'ИнКонт', $contact->other);
            $RekIdentGO->appendChild($contactElement);

        $SodInfGO->appendChild($RekIdentGO);
        $document->appendChild($SodInfGO);

        $parent->appendChild($document);
    }
}
