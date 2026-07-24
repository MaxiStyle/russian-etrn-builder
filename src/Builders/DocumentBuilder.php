<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMDocument;
use DOMElement;
use DOMException;
use MaxiStyle\EtrnBuilder\Entities\Address;

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
}
