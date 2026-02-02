<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвГП"
 * Сведения о грузополучателе
 */
class Consignee extends BaseEntity
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

    /**
     * @var ?Address Адрес доставки груза
     */
    public ?Address $deliveryAddress;
}