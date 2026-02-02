<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвГО"
 * Сведения о грузоотправителе
 */
class Shipper extends BaseEntity
{
    /**
     * @var int Статус грузоотправителя (грузоотправитель является / не является экспедитором)
     */
    public int $status;

    /**
     * @var ?UL Юрлицо (должен быть либо ЮЛ, либо ИП)
     */
    public ?UL $ul;

    /**
     * @var ?IP ИП (должен быть либо ЮЛ, либо ИП)
     */
    public ?IP $ip;

    /**
     * @var ?Address Адрес
     */
    public ?Address $address;

    /**
     * @var ?Contact Контакт
     */
    public ?Contact $contact;
}
