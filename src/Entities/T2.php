<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность Титул T2 — Информация перевозчика о приёме груза к перевозке (ON_TRNACLPPRIN, КНД 1110340)
 */
class T2 extends Document
{
    public string $knd = '1110340';
    public string $formatVersion = '5.01';
    public string $documentName = 'Транспортная накладная, информация перевозчика о приеме груза к перевозке';
    public string $sodOper = 'Груз принят к перевозке водителем, уполномоченным перевозчиком на перевозку груза, от лица, осуществившего погрузку груза в транспортное средство';

    public ?DateTimeInterface $dateFile = null;
    public ?string $fileId = null;
    public ?string $softwareVersion = null;

    /** УИД_ТрН — UUID из T1 */
    public string $documentUuid = '';

    /** Ссылка на файл обмена T1 (ИдИнфГО) */
    public string $t1FileId = '';
    public string $t1FileDate = '';
    public string $t1FileTime = '';
    public string $t1Signature = '';

    public ?Signatory $signatory = null;
}