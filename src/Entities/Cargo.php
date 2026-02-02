<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвГруз"
 * Сведения о грузе
 */
class Cargo extends BaseEntity
{
    /**
     * @var string Отгрузочное наименование груза
     */
    public string $name;

    /**
     * @var string Состояние груза
     */
    public string $condition;

    /**
     * @var string Способ упаковки
     */
    public string $packagingMethod;

    /**
     * @var string Вид тары (обязательно). По классификатору видов тары или «00» при отсутствии тары
     */
    public string $packagingType;

    /**
     * @var int Количество грузовых мест согласно описанию груза (обязательно)
     */
    public int $cargoUnits;

    /**
     * @var string Маркировка
     */
    public string $marking;

    /**
     * @var float Плановая масса груза, в килограммах
     */
    public float $plannedWeight;
}