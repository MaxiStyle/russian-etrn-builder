<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MaxiStyle\EtrnBuilder\DocumentGenerator;
use MaxiStyle\EtrnBuilder\Entities;
use MaxiStyle\EtrnBuilder\Enums\Order;

### ЭЗЗ Т1 — «Информация грузоотправителя» (КНД 1110361, префикс ON_ZAKZVGO) ###

// ========================================
// ГРУЗООТПРАВИТЕЛЬ
// ========================================
$shipperAddr = new Entities\Address();
$shipperAddr->index = '680000';
$shipperAddr->regionCode = '27';
$shipperAddr->city = 'Хабаровск';
$shipperAddr->street = 'Суворова';
$shipperAddr->house = '82А';
$shipperAddr->full = 'Хабаровский край, г Хабаровск, Суворова улица 82А';

$shipperContact = new Entities\Contact();
$shipperContact->phones = ['+7 4212 111 22 33', '+7 4212 111 22 44'];
$shipperContact->emails = ['office@mnogovozoff.ru'];

$shipperUL = new Entities\LegalEntity();
$shipperUL->name = 'ООО "МНОГОВОЗОФФ"';
$shipperUL->inn = '2724218020';
$shipperUL->kpp = '272301001';

$shipper = new Entities\Participant();
$shipper->legalEntity = $shipperUL;
$shipper->address = $shipperAddr;
$shipper->contact = $shipperContact;

// ========================================
// ПЕРЕВОЗЧИК
// ========================================
$carrierAddr = new Entities\Address();
$carrierAddr->index = '190000';
$carrierAddr->regionCode = '78';
$carrierAddr->city = 'Санкт-Петербург';
$carrierAddr->street = 'Невский проспект';
$carrierAddr->house = '28';

$carrierContact = new Entities\Contact();
$carrierContact->phones = ['+7 812 987 65 43'];

$carrierUL = new Entities\LegalEntity();
$carrierUL->name = 'ООО "ПЕРЕВОЗЧИК СПБ"';
$carrierUL->inn = '7801234567';
$carrierUL->kpp = '780101001';

$carrier = new Entities\Participant();
$carrier->legalEntity = $carrierUL;
$carrier->address = $carrierAddr;
$carrier->contact = $carrierContact;

// ========================================
// ПУНКТ ПОДАЧИ ТС
// ========================================
$supply = new Entities\SupplyPoint();
$supply->dateTime = new DateTime('2026-09-20 08:00:00');
$supply->hasTimezone = true; // с координацией UTC
$supply->address = $shipperAddr;

// ========================================
// ПУНКТЫ МАРШРУТА (Погрузка + Выгрузка)
// ========================================
$load = new Entities\RoutePoint();
$load->operation = Order::OPERATION_LOADING;
$load->number = 1;
$load->dateTime = new DateTime('2026-09-20 08:00:00');
$load->hasTimezone = true;
$load->address = $shipperAddr;
$load->infrastructureOwnerName = 'ООО "МНОГОВОЗОФФ"';
$load->infrastructureOwnerInn = '2724218020';

$consigneeAddr = new Entities\Address();
$consigneeAddr->index = '125009';
$consigneeAddr->regionCode = '77';
$consigneeAddr->city = 'Москва';
$consigneeAddr->street = 'Тверская';
$consigneeAddr->house = '15';

$unload = new Entities\RoutePoint();
$unload->operation = Order::OPERATION_UNLOADING;
$unload->number = 2;
$unload->dateTime = new DateTime('2026-09-24 18:00:00');
$unload->hasTimezone = true;
$unload->address = $consigneeAddr;

// ========================================
// ГРУЗ
// ========================================
$cargo = new Entities\OrderCargo();
$cargo->name = 'Строительные материалы';
$cargo->condition = 'Отсутствует';
$cargo->packagingType = '00'; // без тары
$cargo->units = 1;
$cargo->weightMethod = Order::WEIGHT_METHOD_TOTAL;
$cargo->volume = 12.50;
$cargo->distributable = true;
$cargo->divisible = false;
$cargo->grossWeight = 18500.50;
$cargo->height = 2.50;
$cargo->length = 8.00;
$cargo->width = 2.40;

// ========================================
// ТРЕБОВАНИЯ К ТС
// ========================================
$req = new Entities\VehicleRequirements();
$req->type = 'Тентованный';
$req->carryingCapacity = 20.0;
$req->capacity = 80.0;
$req->bodyType = 'Полуприцеп-тент';

// ========================================
// ДОГОВОР ОБ ОРГАНИЗАЦИИ ПЕРЕВОЗОК (обязателен при Функция=Заявка)
// ========================================
$contract = new Entities\DocumentRequisites();
$contract->name = 'Договор об организации перевозок';
$contract->number = 'ДОП-42/2026';
$contract->date = new DateTime('2026-01-15');
$contract->legalParticipants = ['2724218020', '7801234567'];

// ========================================
// ПОДПИСАНТ
// ========================================
$signatory = new Entities\OrderSignatory();
$signatory->position = 'Генеральный директор';
$signatory->authorityConfirmation = Order::AUTHORITY_1; // без доверенности
$signatory->lastName = 'Иванов';
$signatory->firstName = 'Иван';
$signatory->middleName = 'Иванович';

// ========================================
// СБОРКА ДОКУМЕНТА
// ========================================
$doc = new Entities\OrderT1();
$doc->fileId = 'ON_ZAKZVGO_2AE_2AO_20260916_c266b270-886c-4d58-aff4-c68c8b7e21ee';
$doc->softwareVersion = 'MAG 1.0';
$doc->function = Order::FUNCTION_REQUEST; // Заявка
$doc->dateFile = new DateTime('2026-09-16 10:00:00');
$doc->composerName = 'ООО "МНОГОВОЗОФФ"';
$doc->contract = $contract;
$doc->documentUuid = '00000000-0000-0000-0000-000000000001';
$doc->orderNumber = '42/12';
$doc->orderDate = new DateTime('2026-09-16');
$doc->regulatoryInstructions = 'Согласно Правилам перевозок грузов автомобильным транспортом';
$doc->foodTransport = false;
$doc->shipper = $shipper;
$doc->carrier = $carrier;
$doc->supplyPoint = $supply;
$doc->routePoints = [$load, $unload];
$doc->cargos = [$cargo];
$doc->vehicleRequirements = $req;
$doc->signatory = $signatory;

// ========================================
// ГЕНЕРАЦИЯ, ВАЛИДАЦИЯ, СОХРАНЕНИЕ
// ========================================
$generator = new DocumentGenerator();
$generator->setCharset('windows-1251');

$xml = $generator->generate($doc);
$xsd = __DIR__ . '/order_t1.xsd';
$errors = $generator->validate($xml, $xsd);

if ($errors) {
    echo "OrderT1: НЕ ВАЛИДЕН по XSD, ошибок: " . count($errors) . PHP_EOL;
    foreach ($errors as $e) echo "  - {$e}\n";
    exit(1);
}

$generator->saveToFile($xml, __DIR__ . '/order_t1.xml');
echo 'OrderT1 успешно сформирован и валиден по XSD, сохранён в examples/order_t1.xml';
