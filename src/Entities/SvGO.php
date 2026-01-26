<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвГО"
 * Сведения о грузоотправителе
 */
class SvGO extends BaseEntity
{
    /**
     * @var int Статус грузоотправителя (грузоотправитель является / не является экспедитором)
     */
    protected int $goEksp;

    /**
     * @var ?UL Юрлицо (должен быть либо ЮЛ, либо ИП)
     */
    protected ?UL $ul;

    /**
     * @var ?IP ИП (должен быть либо ЮЛ, либо ИП)
     */
    protected ?IP $ip;

    /**
     * @var ?Address Адрес
     */
    protected ?Address $address;

    /**
     * @var ?Contact Контакт
     */
    protected ?Contact $contact;
}
