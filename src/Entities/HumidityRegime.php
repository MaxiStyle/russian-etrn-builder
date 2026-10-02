<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "ВлажнРеж" — влажностный режим перевозки.
 * Атрибуты: @ВлажнНеНиже, @ВлажнНеВыше (N(5.2)). Хотя бы один обязателен.
 */
class HumidityRegime extends BaseEntity
{
    /** @var ?float Влажность не ниже, % (@ВлажнНеНиже) */
    public ?float $min = null;

    /** @var ?float Влажность не выше, % (@ВлажнНеВыше) */
    public ?float $max = null;
}