<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность "Реквизиты документа"
 */
class DocumentRequisites extends BaseEntity
{
    /**
     * @var ?string Наименование документа
     */
    public ?string $name = null;

    /**
     * @var ?string Номер документа
     */
    public ?string $number = null;

    /**
     * @var ?DateTimeInterface Дата документа
     */
    public ?DateTimeInterface $date = null;

    /**
     * @var array ИНН юридических лиц, составивших документ */
    public array $legalParticipants = [];
}
