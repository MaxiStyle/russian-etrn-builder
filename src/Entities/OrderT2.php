<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность файла обмена «Информация перевозчика» (ответ на ЭЗЗ, T2 ЭЗЗ).
 * КНД 1110362, префикс имени файла ON_ZAKZVPER.
 * XSD: examples/order_t2.xsd.
 */
class OrderT2 extends Document
{
    /** @var string Код по КНД (константа) */
    public string $knd = '1110362';

    /** @var string Версия формата (константа) */
    public string $formatVersion = '5.01';

    /** @var ?string Имя файла обмена без расширения (Файл@ИдФайл) */
    public ?string $fileId = null;

    /** @var ?string Версия программы (Файл@ВерсПрог) */
    public ?string $softwareVersion = null;

    /** @var string[] ИдПолИной* */
    public array $otherRecipientIds = [];

    /** @var ?DateTimeInterface Дата и время формирования (@ДатИнфПрв + @ВрИнфПрв) */
    public ?DateTimeInterface $dateFile = null;

    /** @var string Наименование составителя (@НаимЭкСубСост) */
    public string $composerName = '';

    /** @var ?string Основание составителя (ОснДовОргСост) */
    public ?string $composerBasis = null;

    /** @var string ИдФайлИнфГО — имя файла T1 без расширения */
    public string $t1FileId = '';

    /** @var string ДатФайлИнфГО (d.m.Y) */
    public string $t1FileDate = '';

    /** @var string ВрФайлИнфГО (H:i:s) */
    public string $t1FileTime = '';

    /** @var string ЭП T1 (base64) */
    public string $t1Signature = '';

    /** @var string УИД_Зак (СодИнфПрв@УИД_Зак) — из T1 */
    public string $documentUuid = '';

    /**
     * @var string Решение перевозчика (СодИнфПрв@СодОпер):
     * '1' — принят, '2' — отказ. См. Enums\Order::DECISION_*.
     */
    public string $decision = '';

    /** @var ?string Причина отказа (@ПричОтк); обязателен при decision='2' */
    public ?string $rejectReason = null;

    /** @var ?Person Ответственное лицо перевозчика (СвЛицОргПрвз); при decision='1' */
    public ?Person $responsiblePerson = null;

    /** @var Driver[] Водители (СвВодит+); при decision='1', для ЭЗЗ требуется ИНН и ≥1 телефон */
    public array $drivers = [];

    /** @var ?Person Уполномоченное лицо перевозчика (СвЛицПрв) */
    public ?Person $carrierPerson = null;

    /** @var ?Vehicle Транспортное средство (СвТС/ТС); при decision='1' */
    public ?Vehicle $vehicle = null;

    /** @var Vehicle[] Прицепы (СвТС/Прицеп*) */
    public array $trailers = [];

    /** @var string[] Специальные условия движения (СвТС/СпецУслДвиж*) */
    public array $specialTrafficConditions = [];

    /** @var ?Payment Размер платы и порядок расчёта (РазмПлатРасчет); при decision='1' */
    public ?Payment $payment = null;

    /** @var InfoField[] Информационные поля (ИнфПол*) */
    public array $infoFields = [];

    /** @var ?OrderSignatory Подписант (ПодпИнфПрв) */
    public ?OrderSignatory $signatory = null;
}