<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность Титул T1 - Информация о грузе, ТС, водителе
 */
class T1 extends Document
{
    /**
     * @var string Код документа по КНД (константа)
     */
    protected string $knd = '1110339';

    /**
     * @var string Наименование документа по факту хозяйственной жизни (константа)
     */
    protected string $docName = 'Транспортная накладная, информация грузоотправителя';

    /**
     * @var ?DateTimeInterface Дата и время формирования файла обмена информации грузоотправителя
     */
    protected ?DateTimeInterface $dateFile = null;

    /**
     * @var ?SodInfGO Содержание транспортной накладной, информация грузоотправителя
     */
    protected ?SodInfGO $sodInfGO = null;
}
