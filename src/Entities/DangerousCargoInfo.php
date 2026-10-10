<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "ИнфОпасн" — сведения об опасном грузе (СвОпаснГрузТип).
 *
 * По XSD обязательные атрибуты: НомООН, НадОтгНаим, Клас, КласКод, ГрУп, ЗнОп, КодОгрЧерТун.
 * Если ИнфОпасн формируется — должны быть заполнены все обязательные поля (XSD не позволяет
 * частичного блока). Опциональные поля (ТехНаим, Описание, НазРадионук и т.д.) можно расширять
 * по мере необходимости.
 */
class DangerousCargoInfo extends BaseEntity
{
    /** @var ?string Номер ООН груза (@НомООН, required) */
    public ?string $unNumber = null;

    /** @var ?string Надлежащее отгрузочное наименование (@НадОтгНаим, required) */
    public ?string $properShippingName = null;

    /** @var ?string Класс опасности (@Клас, required, до 3 символов) */
    public ?string $class = null;

    /** @var ?string Классификационный код (@КласКод, required) */
    public ?string $classificationCode = null;

    /** @var ?string Группа упаковки (@ГрУп, required) */
    public ?string $packingGroup = null;

    /** @var ?string Знак опасности (@ЗнОп, required) */
    public ?string $dangerSign = null;

    /** @var ?string Код ограничения проезда через тоннели (@КодОгрЧерТун, required) */
    public ?string $tunnelRestrictionCode = null;
}