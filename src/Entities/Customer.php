<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвЗак"
 * Сведения о грузоотправителе
 */
class Customer extends BaseEntity
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
     * @var ?DocumentRequisites Реквизиты договора на выполнение услуг по организации перевозки груза
     */
    public ?DocumentRequisites $contract;
}
