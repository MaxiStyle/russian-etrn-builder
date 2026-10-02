<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность "ПунктПод" — пункт подачи транспортного средства (ЭЗЗ T1).
 *
 * По XSD: атрибуты @ДатВрПод (ДатаВремяВЗТип), @НалКоорТочВрПод, @ПредВрПод? (ВремяВЗТип),
 * @НалКоорТочПредВрПод?; дочерний АдрПунктПод (АдресПользТип).
 */
class SupplyPoint extends BaseEntity
{
    /** @var ?DateTimeInterface Дата и время подачи ТС (@ДатВрПод) */
    public ?DateTimeInterface $dateTime = null;

    /** @var bool Признак координации точного времени UTC (@НалКоорТочВрПод): true = '1' */
    public bool $hasTimezone = false;

    /** @var ?string Предельно допустимое время подачи (@ПредВрПод, формат ВремяВЗТип) */
    public ?string $deadlineTime = null;

    /** @var ?bool Признак координации точного времени для ПредВрПод */
    public ?bool $deadlineHasTimezone = null;

    /** @var ?Address Адрес пункта подачи ТС (АдрПунктПод, АдресПользТип) */
    public ?Address $address = null;
}