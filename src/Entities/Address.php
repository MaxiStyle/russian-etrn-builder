<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "Адрес"
 * Сведения об адресе
 */
class Address extends BaseEntity
{
    /**
     * @var ?string Адрес строкой (если заполнено, то остальные элементы можно не заполнять)
     */
    public ?string $full = null;

    /**
     * @var ?string Индекс
     */
    public ?string $index = null;

    /**
     * @var ?string Код региона
     */
    public ?string $regionCode = null;

    /**
     * @var ?string Район
     */
    public ?string $district = null;

    /**
     * @var ?string Город
     */
    public ?string $city = null;

    /**
     * @var ?string Населенный пункт
     */
    public ?string $settlement = null;

    /**
     * @var ?string Улица
     */
    public ?string $street = null;

    /**
     * @var ?string Дом
     */
    public ?string $house = null;

    /**
     * @var ?string Корпус
     */
    public ?string $building = null;

    /**
     * @var ?string Квартира
     */
    public ?string $flat = null;

    /**
     * @var ?string Код страны
     */
    public ?string $countryCode = null;
}
