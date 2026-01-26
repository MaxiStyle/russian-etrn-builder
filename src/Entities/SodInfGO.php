<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СодИнфГО"
 * Содержание транспортной накладной, информация грузоотправителя
 */
class SodInfGO extends BaseEntity
{
    /**
     * @var ?string Содержание операции (константа)
     */
    protected ?string $operation = 'Лицом, осуществляющим погрузку груза, при указанных обстоятельствах передан водителю груз с указанными характеристиками';

    /**
     * @var ?SvGO Сведения о грузоотправителе
     */
    protected ?SvGO $svGO = null;
}
