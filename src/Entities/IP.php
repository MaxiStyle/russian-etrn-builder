<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвИПТрНТип"
 * Сведения об индивидуальном предпринимателе
 */
class IP extends BaseEntity
{
    /**
     * @var ?string Фамилия
     */
    public ?string $lastName;

    /**
     * @var ?string Имя
     */
    public ?string $firstName;

    /**
     * @var ?string Отчество (не обязательно)
     */
    public ?string $middleName;

    /**
     * @var ?string ИНН физлица
     */
    public ?string $inn;

    /**
     * @var ?string ОГРНИП
     */
    public ?string $ogrnip;

    /**
     * @var ?string Иные сведения, идентифицирующие физическое лицо (не обязательно)
     */
    public ?string $other;
}
