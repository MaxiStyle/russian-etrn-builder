<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвТС"
 * Сведения о транспортном средстве
 */
class Vehicle extends BaseEntity
{
    /**
     * @var string Регистрационный номер
     */
    public string $regNumber;

    /**
     * @var int Тип владения (1 собственность, 2 совместная собственность супругов, 3 аренда, 4 лизинг, 5 безвозмездное пользование)
     */
    public int $ownershipType;

    /**
     * @var string Тип транспортного средства
     */
    public string $type;

    /**
     * @var string Марка транспортного средства
     */
    public string $brand;

    /**
     * @var float Грузоподъемность в тоннах
     */
    public float $carryingCapacity;

    /**
     * @var float Вместимость в кубических метрах
     */
    public float $capacity;
}