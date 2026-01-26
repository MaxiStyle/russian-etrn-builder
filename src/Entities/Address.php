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
    protected ?string $full = null;

    /**
     * @var ?string Индекс
     */
    protected ?string $index = null;

    /**
     * @var ?string Код региона
     */
    protected ?string $regionCode = null;

    /**
     * @var ?string Район
     */
    protected ?string $district = null;

    /**
     * @var ?string Город
     */
    protected ?string $city = null;

    /**
     * @var ?string Населенный пункт
     */
    protected ?string $settlement = null;

    /**
     * @var ?string Улица
     */
    protected ?string $street = null;

    /**
     * @var ?string Дом
     */
    protected ?string $house = null;

    /**
     * @var ?string Корпус
     */
    protected ?string $building = null;

    /**
     * @var ?string Квартира
     */
    protected ?string $flat = null;

    /**
     * @var ?string Код страны
     */
    protected ?string $countryCode = null;
}
