<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность "СвДоверБум" — сведения о бумажной доверенности подписанта.
 * Используется в блоке подписанта при СпосПодтПолном=5.
 * Точные имена атрибутов — по XSD ФНС.
 */
class PaperPoa extends BaseEntity
{
    /** @var ?string Номер доверенности */
    public ?string $number = null;

    /** @var ?DateTimeInterface Дата выдачи */
    public ?DateTimeInterface $date = null;

    /** @var ?string Кем выдана */
    public ?string $issuedBy = null;
}