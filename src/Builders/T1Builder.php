<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMException;
use MaxiStyle\EtrnBuilder\Entities\T1;
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
        if (!$doc instanceof T1) {
            throw new \InvalidArgumentException('Документ должен быть экземпляром T1');
        }

        $document = $dom->createElement('Документ');

        $document->setAttribute('КНД', $doc->get('knd'));
        $document->setAttribute('ПоФактХЖ', $doc->get('docName'));
        $document->setAttribute('ДатИнфГО', $doc->get('dateFile')->format('d.m.Y'));
        $document->setAttribute('ВрИнфГО',$doc->get('dateFile')->format('H:i:s'));

        // Содержание транспортной накладной, информация грузоотправителя
        $go = $doc->get('sodInfGO');
        $svGO = $go->get('svGO');
        
        $SodInfGO = $dom->createElement('СодИнфГО');
        $SodInfGO->setAttribute('СодОпер', $go->get('operation'));
        $SodInfGO->setAttribute('НомерТрН', $doc->get('numTrN'));
        $SodInfGO->setAttribute('ДатаТрН', $doc->get('dateTrN')->format('d.m.Y'));
        $SodInfGO->setAttribute('НомЗак', $doc->get('numOrder'));
        $SodInfGO->setAttribute('ДатаЗак', $doc->get('dateOrder')->format('d.m.Y'));
        $SodInfGO->setAttribute('ГОЭксп', $svGO->get('goEksp'));

        // Грузоотправитель
        $RekIdentGO = $dom->createElement('РекИдентГО');
            $IdSv = $dom->createElement('ИдСв');
                if ($svGO->get('ul') !== null) {
                    $ul = $svGO->get('ul');
                    $SvYuLUch = $dom->createElement('СвЮЛУч');
                    $SvYuLUch->setAttribute('НаимОрг', $ul->get('name'));
                    $SvYuLUch->setAttribute('ИННЮЛ', $ul->get('inn'));
                    $SvYuLUch->setAttribute('КПП', $ul->get('kpp'));
                    $IdSv->appendChild($SvYuLUch);
                } else if ($svGO->get('ip') !== null) {
                    $ip = $svGO->get('ip');
                    $SvIP = $dom->createElement('СвИП');
                    $SvIP->setAttribute('ФИО', $ip->get('fio'));
                    $SvIP->setAttribute('ИННФЛ', $ip->get('inn'));
                    $SvIP->setAttribute('ОГРНИП', $ip->get('ogrnip'));
                    $SvIP->setAttribute('ИныеСвед', $ip->get('other'));
                    $IdSv->appendChild($SvIP);
                }
            $RekIdentGO->appendChild($IdSv);

            $addr = $dom->createElement('Адрес');
            $address = $svGO->get('address');
            $addressElement = $dom->createElement('АдрРФ');
            $addressElement->setAttribute('Индекс', $address->get('index') ?? '');
            $addressElement->setAttribute('КодРегион', $address->get('regionCode') ?? '');
            $addressElement->setAttribute('Район', $address->get('district') ?? '');
            $addressElement->setAttribute('Город', $address->get('city') ?? '');
            $addressElement->setAttribute('НаселПункт', $address->get('settlement') ?? '');
            $addressElement->setAttribute('Улица', $address->get('street') ?? '');
            $addressElement->setAttribute('Дом', $address->get('house') ?? '');
            $addressElement->setAttribute('Корпус', $address->get('building') ?? '');
            $addressElement->setAttribute('Кварт', $address->get('flat') ?? '');
            $addressElement->setAttribute('КодСтр', $address->get('countryCode') ?? '');
            $addressElement->setAttribute('АдрТекст', $address->get('full') ?? '');
            $addr->appendChild($addressElement);
            $RekIdentGO->appendChild($addr);

            $contact = $svGO->get('contact');
            $contactElement = $dom->createElement('Контакт');
            $this->append($dom, $contactElement, 'Тлф', $contact->get('phone'));
            $this->append($dom, $contactElement, 'ЭлПочта', $contact->get('email'));
            $this->append($dom, $contactElement, 'ИнКонт', $contact->get('other'));
            $RekIdentGO->appendChild($contactElement);

        $SodInfGO->appendChild($RekIdentGO);
        $document->appendChild($SodInfGO);

        $parent->appendChild($document);
    }
}
