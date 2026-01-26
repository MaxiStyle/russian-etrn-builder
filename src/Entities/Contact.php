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
    protected ?string $phone = null;

    /**
     * @var ?string Адрес электронной почты
     */
    protected ?string $email = null;

    /**
     * @var ?string Иные контактные данные
     */
    protected ?string $other = null;

}
