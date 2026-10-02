<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность "АдрПункт" — пункт маршрута (погрузка/выгрузка) ЭЗЗ T1.
 *
 * По XSD: атрибуты @Опер (Погрузка|Выгрузка), @ПорНомПункт?, @ДатВрОпер?, @НалКоорТочВрОпер?,
 * @ПредВрОпер?, @НалКоорТочПредВрОпер?; дочерние АдресПункт (АдресПользТип), ОргВладИнфр?
 * (обязателен при @Опер='Погрузка').
 */
class RoutePoint extends BaseEntity
{
    /** @var string Значение справочника OPERATION_LOADING/OPERATION_UNLOADING (@Опер) */
    public string $operation = '';

    /** @var ?int Порядковый номер пункта (@ПорНомПункт) */
    public ?int $number = null;

    /** @var ?DateTimeInterface Дата и время операции (@ДатВрОпер) */
    public ?DateTimeInterface $dateTime = null;

    /** @var ?bool Признак координации точного времени UTC (@НалКоорТочВрОпер) */
    public ?bool $hasTimezone = null;

    /** @var ?string Предельное время операции (@ПредВрОпер, ВремяВЗТип) */
    public ?string $deadlineTime = null;

    /** @var ?bool Признак координации точного времени для ПредВрОпер */
    public ?bool $deadlineHasTimezone = null;

    /** @var ?Address Адрес пункта (АдресПункт) */
    public ?Address $address = null;

    /**
     * @var ?string Наименование организации-владельца объекта инфраструктуры (ОргВладИнфр@НаимВладИнфр).
     * Обязателен при @Опер='Погрузка'. По XSD ЭЗЗ ОргВладИнфр — это простой элемент с двумя
     * обязательными атрибутами (НаимВладИнфр + ИННВладИнфр), не УчастникТип.
     */
    public ?string $infrastructureOwnerName = null;

    /** @var ?string ИНН организации-владельца объекта инфраструктуры (ОргВладИнфр@ИННВладИнфр). */
    public ?string $infrastructureOwnerInn = null;
}