<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность "СвДоверЭл" — сведения об электронной МЧД подписанта.
 * Используется в блоке подписанта при СпосПодтПолном=3.
 * Точные имена атрибутов — по XSD ФНС (см. §15.1 ТЗ_заказ-заявка.md).
 */
class ElectronicPoa extends BaseEntity
{
    /** @var ?string Номер МЧД */
    public ?string $number = null;

    /** @var ?DateTimeInterface Дата МЧД */
    public ?DateTimeInterface $date = null;

    /** @var ?string ИНН доверителя */
    public ?string $delegatorInn = null;
}