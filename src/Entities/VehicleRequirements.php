<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "ПарТСПрвз" — требования к транспортному средству для ЭЗЗ T1.
 * Атрибуты: @Тип (О), @Грузопод (т, N(5.2), О), @Вместим (м³, О), @ТипПогр?, @ТипКуз?, @ДрПарам?.
 */
class VehicleRequirements extends BaseEntity
{
    /** @var string Тип ТС (@Тип) */
    public string $type = '';

    /** @var float Грузоподъёмность, т (@Грузопод) */
    public float $carryingCapacity = 0.0;

    /** @var float Вместимость, м³ (@Вместим) */
    public float $capacity = 0.0;

    /** @var ?string Тип погрузки (@ТипПогр) */
    public ?string $loadingType = null;

    /** @var ?string Тип кузова (@ТипКуз) */
    public ?string $bodyType = null;

    /** @var ?string Другие параметры (@ДрПарам) */
    public ?string $otherParams = null;
}