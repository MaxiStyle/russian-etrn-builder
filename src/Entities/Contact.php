<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "Контакт"
 * Контактные данные
 */
class Contact extends BaseEntity
{
    /**
     * @var ?string Номер контактного телефона
     */
    public ?string $phone = null;

    /**
     * @var ?string Адрес электронной почты
     */
    public ?string $email = null;

    /**
     * @var ?string Иные контактные данные
     */
    public ?string $other = null;

}
