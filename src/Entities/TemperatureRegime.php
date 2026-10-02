<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "ТемпРеж" — температурный режим перевозки.
 * Атрибуты: @ТемпНеНиже, @ТемпНеВыше (N(6.2)). Хотя бы один обязателен, если элемент присутствует.
 */
class TemperatureRegime extends BaseEntity
{
    /** @var ?float Температура не ниже, °C (@ТемпНеНиже) */
    public ?float $min = null;

    /** @var ?float Температура не выше, °C (@ТемпНеВыше) */
    public ?float $max = null;
}