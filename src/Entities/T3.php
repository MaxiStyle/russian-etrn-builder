<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность Титул T3 — Информация грузополучателя о приёме груза (ON_TRNACLGRPO, КНД 1110341)
 */
class T3 extends Document
{
    public string $knd = '1110341';
    public string $formatVersion = '5.01';
    public string $documentName = 'Транспортная накладная, информация грузополучателя о приеме груза';
    public string $sodOper = 'Лицом, управомоченным на получение груза, при указанных обстоятельствах принят от перевозчика груз с указанными характеристиками';

    public ?DateTimeInterface $dateFile = null;
    public ?string $fileId = null;
    public ?string $softwareVersion = null;

    /** НаимЭконСубСост — наименование организации, составившей документ */
    public string $composerName = '';

    /** УИД_ТрН — UUID из T1/T2 */
    public string $documentUuid = '';

    /** Ссылка на файл обмена T2 (ИдИнфПрвПрием) */
    public string $t2FileId = '';
    public string $t2FileDate = '';
    public string $t2FileTime = '';
    public string $t2Signature = '';

    /** Приём груза грузополучателем (ПриемГрузГП) */
    public string $actualArrival = '';      // ФДатВрПриб
    public string $actualDeparture = '';    // ФДатВрУбыт
    public string $plannedArrival = '';     // ЗаявДатВрПриб
    public string $weightDeterminationMethod = '01'; // МетОпрМасс
    public int $receivedUnits = 1;          // КолМестПриемЧ
    public string $overallCondition = '-';  // ОбщСвСост
    public ?int $receivedGrossWeight = null; // МасБрутЗначПрием (кг)

    /** Адрес выгрузки (АдрВыгруз) */
    public ?Address $unloadingAddress = null;

    /** Сведения о принятом грузе (СвПринПоНаим) */
    public string $cargoName = '';
    public string $cargoCondition = '-';
    public string $cargoMarking = '';
    public int $cargoUnits = 1;
    public ?int $cargoGrossWeight = null;

    public ?Signatory $signatory = null;
}
