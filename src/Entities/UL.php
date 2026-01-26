<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвЮЛУчТрНТип"
 * Сведения о юридическом лице
 */
class UL extends BaseEntity
{
    /**
     * @var ?string Наименование полное
     */
    protected ?string $name = null;

    /**
     * @var ?string ИНН
     */
    protected ?string $inn = null;

    /**
     * @var ?string КПП
     */
    protected ?string $kpp = null;
}
