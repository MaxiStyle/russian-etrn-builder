<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMDocument;
use DOMElement;
use DOMException;
use MaxiStyle\EtrnBuilder\Entities\Address;
use MaxiStyle\EtrnBuilder\Entities\DocumentRequisites;

class DocumentBuilder
{
    /**
     * @throws DOMException
     */
    public function append(DOMDocument &$dom, DOMElement &$parent, string $name, ?string $value): void
    {
        if ($value !== null) {
            $parent->appendChild($dom->createElement($name, $value));
        }
    }

    /**
     * Добавляет РФ-адрес в контейнер (АдресДостГр/ФАдресПогр/АдрВыгруз):
     * - вешает АдрКоммент на сам контейнер (полная строка адреса);
     * - создаёт дочерний АдресРФ с частями (Индекс/КодРегион/Район/Город/...).
     */
    public function appendAddressRf(DOMDocument $dom, DOMElement $wrapper, Address $address): void
    {
        if (!empty($address->full)) {
            $wrapper->setAttribute('АдрКоммент', $address->full);
        }

        $adr = $dom->createElement('АдресРФ');
        $adr->setAttribute('Индекс', $address->index ?? '');
        $adr->setAttribute('КодРегион', $address->regionCode ?? '');
        if ($address->district)   $adr->setAttribute('Район', $address->district);
        if ($address->city)       $adr->setAttribute('Город', $address->city);
        if ($address->settlement) $adr->setAttribute('НаселПункт', $address->settlement);
        if ($address->street)     $adr->setAttribute('Улица', $address->street);
        if ($address->house)      $adr->setAttribute('Дом', $address->house);
        if ($address->building)   $adr->setAttribute('Корпус', $address->building);
        if ($address->flat)       $adr->setAttribute('Кварт', $address->flat);
        $wrapper->appendChild($adr);
    }

    /**
     * Добавляет ОснАрЛиз (реквизиты документа-основания аренды/лизинга/безвозмездного пользования)
     * в ТС/Прицеп. Обязателен при ТипВлад ∈ {3, 4, 5} по XSD.
     * Если номер не задан — используется «Без номера».
     */
    public function appendRentLeaseDocument(DOMDocument $dom, DOMElement $parent, ?DocumentRequisites $doc): void
    {
        if ($doc === null) {
            return;
        }

        $osn = $dom->createElement('ОснАрЛиз');
        if (!empty($doc->name)) $osn->setAttribute('НаимДок', $doc->name);
        $osn->setAttribute('НомерДок', !empty($doc->number) ? $doc->number : 'Без номера');
        if ($doc->date) $osn->setAttribute('ДатаДок', $doc->date->format('d.m.Y'));

        foreach ($doc->legalParticipants as $participant) {
            $this->appendIdRekSost($dom, $osn, (string)$participant);
        }

        $parent->appendChild($osn);
    }

    /**
     * Добавляет ИдРекСост с элементом ИННЮЛ (10 цифр) или ИННФЛ (12 цифр) в зависимости от длины ИНН.
     */
    public function appendIdRekSost(DOMDocument $dom, DOMElement $parent, string $inn): void
    {
        if ($inn === '') {
            return;
        }
        $idRekSost = $dom->createElement('ИдРекСост');
        $tag = strlen($inn) === 12 ? 'ИННФЛ' : 'ИННЮЛ';
        $this->append($dom, $idRekSost, $tag, $inn);
        $parent->appendChild($idRekSost);
    }
}
