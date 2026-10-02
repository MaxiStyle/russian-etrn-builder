<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвИнНеУч" (Сведения об иностранном лице, не состоящем на учёте в РФ).
 * Используется в блоке ИдСв УчастникТип ЭЗЗ. Первая версия — минимальные поля;
 * точные имена атрибутов — по XSD ФНС (СвИнНеУчТип).
 */
class ForeignEntity extends BaseEntity
{
    /** @var ?string Статус лица (@ИдСтат) — по классификатору формата ФНС */
    public ?string $idStat = null;

    /** @var ?string Код страны (@Стран) — цифровой код по ОКСМ */
    public ?string $countryCode = null;

    /** @var ?string Наименование (@Наим) */
    public ?string $name = null;

    /** @var ?string Идентификатор в стране регистрации (@Идентиф) */
    public ?string $identification = null;
}