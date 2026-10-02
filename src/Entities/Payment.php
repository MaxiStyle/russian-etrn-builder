<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "РазмПлатРасчет" — размер платы и порядок расчёта (T2 ЭЗЗ).
 * Атрибуты: @КодОКВ (например, '643'), @ВалНаим, @СтТовБезНДС (N(19.2)),
 * @НалСт ('0%'/'10%'/'20%'/'10/110'/'20/120'/'без НДС'/'исчисляется агентом'),
 * @СтТовУчНал (N(19.2)), @Расчет (текст).
 */
class Payment extends BaseEntity
{
    /** @var string Код валюты (@КодОКВ), для RUR — '643' */
    public string $currencyCode = '643';

    /** @var ?string Наименование валюты (@ВалНаим) */
    public ?string $currencyName = null;

    /** @var ?float Стоимость товара без НДС (@СтТовБезНДС) */
    public ?float $amountWithoutVat = null;

    /** @var ?string Ставка НДС (@НалСт) — значения из констант Enums\Order::VAT_* */
    public ?string $vatRate = null;

    /** @var ?float Стоимость товара с учётом НДС (@СтТовУчНал) */
    public ?float $amountWithVat = null;

    /** @var ?string Порядок расчёта (@Расчет, произвольный текст) */
    public ?string $settlement = null;
}