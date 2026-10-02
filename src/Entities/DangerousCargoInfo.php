<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "ИнфОпасн" — сведения об опасном грузе (СвОпаснГрузТип).
 *
 * Первая версия — минимальный набор для формирования валидного XML:
 * номер ООН, надлежащее отгрузочное наименование, класс, группа упаковки, код ограничения тоннелей.
 * Полный ДОПОГ-набор (~30 атрибутов) — вне первой версии; расширять здесь без ломки API.
 */
class DangerousCargoInfo extends BaseEntity
{
    /** @var ?string Номер ООН груза */
    public ?string $unNumber = null;

    /** @var ?string Надлежащее отгрузочное наименование */
    public ?string $properShippingName = null;

    /** @var ?string Класс опасности */
    public ?string $class = null;

    /** @var ?string Группа упаковки */
    public ?string $packingGroup = null;

    /** @var ?string Код ограничения проезда через тоннели */
    public ?string $tunnelRestrictionCode = null;
}