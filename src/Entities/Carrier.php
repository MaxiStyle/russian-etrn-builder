<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвПер"
 * Сведения о перевозчике
 */
class Carrier extends BaseEntity
{
    /**
     * @var ?LegalEntity Юрлицо (должен быть либо ЮЛ, либо ИП)
     */
    public ?LegalEntity $legalEntity;

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