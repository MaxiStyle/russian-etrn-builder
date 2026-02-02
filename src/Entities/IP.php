<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвИПТрНТип"
 * Сведения об индивидуальном предпринимателе
 */
class IP extends BaseEntity
{
    /**
     * @var ?string ФИО
     */
    public ?string $fio;

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
