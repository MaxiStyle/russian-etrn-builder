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
    protected ?string $numTrN = null;

    /**
     * @var ?string Порядковый номер заказа (заявки)
     */
    protected ?string $numOrder = null;

    /**
     * @var ?DateTimeInterface Дата составления транспортной накладной
     */
    protected ?DateTimeInterface $dateTrN = null;

    /**
     * @var ?DateTimeInterface Дата заказа (заявки)
     */
    protected ?DateTimeInterface $dateOrder = null;
}
