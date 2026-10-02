<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвФЛУчаст" (Сведения о физическом лице — участнике)
 * Используется в блоке ИдСв УчастникТип ЭЗЗ.
 */
class Individual extends BaseEntity
{
    /** @var ?string ИННФЛ (12 цифр) */
    public ?string $inn = null;

    /** @var ?string Фамилия */
    public ?string $lastName = null;

    /** @var ?string Имя */
    public ?string $firstName = null;

    /** @var ?string Отчество */
    public ?string $middleName = null;

    /** @var ?string Иные сведения, идентифицирующие физическое лицо */
    public ?string $other = null;
}