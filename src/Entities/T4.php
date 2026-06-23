<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность Титул T4 — Информация перевозчика о выдаче груза грузополучателю
 * (ON_TRNACLPVYN, КНД 1110342)
 */
class T4 extends Document
{
    public string $knd = '1110342';
    public string $formatVersion = '5.01';
    public string $documentName = 'Транспортная накладная, информация перевозчика о выдаче груза грузополучателю';
    public string $sodOper = 'Груз с указанными характеристиками сдан водителем, уполномоченным перевозчиком на перевозку груза, лицу, управомоченному на получение груза';

    public ?DateTimeInterface $dateFile = null;
    public ?string $fileId = null;
    public ?string $softwareVersion = null;

    /** УИД_ТрН — UUID из цепочки T1-T3 */
    public string $documentUuid = '';

    /** Ссылка на файл обмена T3 (ИдИнфГП) */
    public string $t3FileId = '';
    public string $t3FileDate = '';
    public string $t3FileTime = '';
    public string $t3Signature = '';

    public ?Signatory $signatory = null;
}
