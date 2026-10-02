<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность файла обмена «Информация грузоотправителя» (Электронный заказ/заявка, T1 ЭЗЗ).
 * КНД 1110361, префикс имени файла ON_ZAKZVGO.
 * XSD: examples/order_t1.xsd.
 */
class OrderT1 extends Document
{
    /** @var string Код по КНД (константа) */
    public string $knd = '1110361';

    /** @var string Версия формата (константа) */
    public string $formatVersion = '5.01';

    /** @var ?string Имя файла обмена без расширения (Файл@ИдФайл) */
    public ?string $fileId = null;

    /** @var ?string Версия программы (Файл@ВерсПрог) */
    public ?string $softwareVersion = null;

    /**
     * @var string[] Идентификаторы иных получателей файла обмена (Файл@ИдПолИной),
     * формат «ИдОЭДО_КодПолИной», см. также {@see T1::$recipientId}
     */
    public array $otherRecipientIds = [];

    /**
     * @var string Функция документа (Документ@Функция): 'Заказ' или 'Заявка'.
     * Значения см. Enums\Order::FUNCTION_ORDER / FUNCTION_REQUEST.
     */
    public string $function = '';

    /** @var ?DateTimeInterface Дата и время формирования (@ДатИнфГО + @ВрИнфГО) */
    public ?DateTimeInterface $dateFile = null;

    /** @var string Наименование составителя (@НаимЭкСубСост) */
    public string $composerName = '';

    /** @var ?string Основание, по которому составитель — не грузоотправитель (ОснДовОргСост) */
    public ?string $composerBasis = null;

    /** @var ?DocumentRequisites Реквизиты договора об организации перевозок (ДогОргПрвз).
     *  Обязателен при function = 'Заявка'.
     */
    public ?DocumentRequisites $contract = null;

    /** @var ?string УИД_Зак (СодИнфГО@УИД_Зак) — из пула ГИС ЭПД */
    public ?string $documentUuid = null;

    /**
     * @var string Содержание операции (СодИнфГО@СодОпер). По XSD ЭЗЗ значение фиксировано —
     * 'Предоставление заказа и заявки на перевозку груза автомобильным транспортом'.
     */
    public string $operationContent = 'Предоставление заказа и заявки на перевозку груза автомобильным транспортом';

    /** @var string Номер заказа/заявки (@НомЗак) */
    public string $orderNumber = '';

    /** @var ?DateTimeInterface Дата заказа/заявки (@ДатаЗак) */
    public ?DateTimeInterface $orderDate = null;

    /** @var string Указания по нормам перевозки (@УкНормПрвз) */
    public string $regulatoryInstructions = '';

    /** @var ?string Предельный срок перевозки (@ПредСрПрвз) */
    public ?string $deadline = null;

    /** @var bool Перевозка пищевой продукции (@ПрвзПищПрод); в XML — '0'/'1' */
    public bool $foodTransport = false;

    /** @var ?string Иные условия (@ИныеУслв) */
    public ?string $otherConditions = null;

    /** @var ?string Конечный пункт маршрута (@КонПунктМрш) */
    public ?string $routeEndPoint = null;

    /** @var string[] Промежуточные пункты маршрута (@ПромПунктМрш*) */
    public array $routeIntermediatePoints = [];

    /** @var ?Participant Грузоотправитель (СвГО) */
    public ?Participant $shipper = null;

    /** @var ?Participant Перевозчик (СвПрв) */
    public ?Participant $carrier = null;

    /** @var Person[] Уполномоченные лица грузоотправителя (СвЛицГО*) */
    public array $shipperPersons = [];

    /** @var ?SupplyPoint Пункт подачи ТС (ПунктПод) */
    public ?SupplyPoint $supplyPoint = null;

    /** @var RoutePoint[] Пункты маршрута (АдрПункт+), ≥1 Погрузка и ≥1 Выгрузка */
    public array $routePoints = [];

    /** @var OrderCargo[] Описания грузов (ОпГруз+) */
    public array $cargos = [];

    /** @var ?VehicleRequirements Требования к ТС (ПарТСПрвз) */
    public ?VehicleRequirements $vehicleRequirements = null;

    /** @var ?TemperatureRegime Температурный режим (ТемпРеж) */
    public ?TemperatureRegime $temperature = null;

    /** @var ?HumidityRegime Влажностный режим (ВлажнРеж) */
    public ?HumidityRegime $humidity = null;

    /** @var InfoField[] Дополнительные информационные поля (ИнфПол*) */
    public array $infoFields = [];

    /** @var ?OrderSignatory Подписант (ПодпИнфГО) */
    public ?OrderSignatory $signatory = null;
}