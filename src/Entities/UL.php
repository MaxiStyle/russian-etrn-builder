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
    public ?string $name = null;

    /**
     * @var ?string ИНН
     */
    public ?string $inn = null;

    /**
     * @var ?string КПП
     */
    public ?string $kpp = null;
}
