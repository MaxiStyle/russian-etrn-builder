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
    protected ?string $fio;

    /**
     * @var ?string ИНН физлица
     */
    protected ?string $inn;

    /**
     * @var ?string ОГРНИП
     */
    protected ?string $ogrnip;

    /**
     * @var ?string Иные сведения, идентифицирующие физическое лицо (не обязательно)
     */
    protected ?string $other;
}
