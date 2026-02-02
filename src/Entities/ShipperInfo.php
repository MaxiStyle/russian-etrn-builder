<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СодИнфГО"
 * Содержание транспортной накладной, информация грузоотправителя
 */
class ShipperInfo extends BaseEntity
{
    /**
     * @var ?string Содержание операции (константа)
     */
    public ?string $operation = 'Лицом, осуществляющим погрузку груза, при указанных обстоятельствах передан водителю груз с указанными характеристиками';

    /**
     * @var ?Shipper Сведения о грузоотправителе
     */
    public ?Shipper $shipper = null;
}
