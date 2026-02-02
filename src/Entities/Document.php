<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность "Документ"
 */
abstract class Document extends BaseEntity
{
    /**
     * @var ?string Порядковый номер транспортной накладной
     */
    public ?string $numTrN = null;

    /**
     * @var ?string Порядковый номер заказа (заявки)
     */
    public ?string $numOrder = null;

    /**
     * @var ?DateTimeInterface Дата составления транспортной накладной
     */
    public ?DateTimeInterface $dateTrN = null;

    /**
     * @var ?DateTimeInterface Дата заказа (заявки)
     */
    public ?DateTimeInterface $dateOrder = null;
}
