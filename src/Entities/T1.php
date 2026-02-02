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
    public string $knd = '1110339';

    /**
     * @var string Наименование документа по факту хозяйственной жизни (константа)
     */
    public string $docName = 'Транспортная накладная, информация грузоотправителя';

    /**
     * @var ?DateTimeInterface Дата и время формирования файла обмена информации грузоотправителя
     */
    public ?DateTimeInterface $dateFile = null;

    /**
     * @var ?ShipperInfo Содержание транспортной накладной, информация грузоотправителя
     */
    public ?ShipperInfo $shipperInfo = null;
}
