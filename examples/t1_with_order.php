<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MaxiStyle\EtrnBuilder\DocumentGenerator;
use MaxiStyle\EtrnBuilder\Entities;

### T1 ЭТрН со ссылкой на подписанный электронный заказ/заявку (блок ИдЗак) ###

$contactShipper = new Entities\Contact();
$contactShipper->phone = '+7 4212 111 22 33';

$addressShipper = new Entities\Address();
$addressShipper->index = '680000';
$addressShipper->regionCode = '27';
$addressShipper->city = 'Хабаровск';
$addressShipper->street = 'Суворова';
$addressShipper->house = '82А';
$addressShipper->countryCode = '643';

$legalEntityShipper = new Entities\LegalEntity();
$legalEntityShipper->name = 'ООО "МНОГОВОЗОФФ"';
$legalEntityShipper->inn = '2724218020';
$legalEntityShipper->kpp = '272301001';

$svGO = new Entities\Shipper();
$svGO->status = 0;
$svGO->legalEntity = $legalEntityShipper;
$svGO->address = $addressShipper;
$svGO->contact = $contactShipper;
$sodInfGO = new Entities\Shipment();

$contactConsignee = new Entities\Contact();
$contactConsignee->phone = '+7 495 123 45 67';
$addressConsignee = new Entities\Address();
$addressConsignee->index = '125009';
$addressConsignee->regionCode = '77';
$addressConsignee->city = 'Москва';
$addressConsignee->street = 'Тверская';
$addressConsignee->house = '15';
$addressConsignee->flat = '101';
$addressConsignee->countryCode = '643';
$legalEntityConsignee = new Entities\LegalEntity();
$legalEntityConsignee->name = 'ООО "ПОЛУЧАТЕЛЬ ЛТД"';
$legalEntityConsignee->inn = '7708123456';
$legalEntityConsignee->kpp = '770801001';
$deliveryAddress = new Entities\Address();
$deliveryAddress->index = '143000';
$deliveryAddress->regionCode = '50';
$deliveryAddress->city = 'Одинцово';
$deliveryAddress->street = 'Ленина';
$deliveryAddress->house = '25';
$deliveryAddress->countryCode = '643';
$deliveryAddress->full = 'Московская область, г. Одинцово, ул. Ленина, д. 25';
$consignee = new Entities\Consignee();
$consignee->legalEntity = $legalEntityConsignee;
$consignee->address = $addressConsignee;
$consignee->contact = $contactConsignee;
$consignee->deliveryAddress = $deliveryAddress;

$contactCarrier = new Entities\Contact();
$contactCarrier->phone = '+7 812 987 65 43';
$addressCarrier = new Entities\Address();
$addressCarrier->index = '191186';
$addressCarrier->regionCode = '78';
$addressCarrier->city = 'Санкт-Петербург';
$addressCarrier->street = 'Невский проспект';
$addressCarrier->house = '28';
$addressCarrier->countryCode = '643';
$legalEntityCarrier = new Entities\LegalEntity();
$legalEntityCarrier->name = 'ООО "ПЕРЕВОЗЧИК СПБ"';
$legalEntityCarrier->inn = '7801234567';
$legalEntityCarrier->kpp = '780101001';
$carrier = new Entities\Carrier();
$carrier->legalEntity = $legalEntityCarrier;
$carrier->address = $addressCarrier;
$carrier->contact = $contactCarrier;

$driver = new Entities\Driver();
$driver->licenseSeries = '78 АБ';
$driver->licenseNumber = '123456';
$driver->licenseIssueDate = '15.06.2020';
$driver->phone = '+7 900 555 77 88';
$driver->lastName = 'Иванов';
$driver->firstName = 'Петр';
$driver->middleName = 'Сидорович';

$vehicle = new Entities\Vehicle();
$vehicle->regNumber = 'А123ВС78';
$vehicle->ownershipType = 1;
$vehicle->type = 'Грузовой автомобиль';
$vehicle->brand = 'КАМАЗ';
$vehicle->carryingCapacity = 20.0;
$vehicle->capacity = 80.0;

$cargo = new Entities\Cargo();
$cargo->name = 'Строительные материалы';
$cargo->condition = 'Новый';
$cargo->packagingMethod = 'Навалом';
$cargo->packagingType = '00';
$cargo->cargoUnits = 1;
$cargo->marking = 'Без маркировки';
$cargo->plannedWeight = 18500.5;

$loadingAddress = new Entities\Address();
$loadingAddress->index = '680000';
$loadingAddress->regionCode = '27';
$loadingAddress->city = 'Хабаровск';
$loadingAddress->street = 'Суворова';
$loadingAddress->house = '82А';
$loadingAddress->countryCode = '643';
$loadingAddress->full = 'Хабаровский край, г Хабаровск, Суворова улица 82А';

$route = new Entities\Route();
$route->plannedArrival = '15.01.2024T09:00:00+10:00';
$route->actualArrival = '15.01.2024T09:15:00+10:00';
$route->actualDeparture = '15.01.2024T11:30:00+10:00';
$route->grossWeight = 18500.5;
$route->weightDeterminationMethod = '01';
$route->cargoUnitsReceived = 1;
$route->loadingAddress = $loadingAddress;

$conditions = new Entities\Conditions();
$conditions->noTransshipment = '1';
$conditions->deliveryDateTime = '17.01.2024T18:00:00+03:00';
$conditions->redirectionPerson = 'Грузоотправитель';
$conditions->redirectionMethod = 'Электронное уведомление перевозчика о переадресовке';
$conditions->contactInfo = '+7 222 888 44 44';

$signatory = new Entities\Signatory();
$signatory->status = '1';
$signatory->lastName = 'Петров';
$signatory->firstName = 'Алексей';
$signatory->middleName = 'Викторович';

$doc = new Entities\T1();
$doc->fileId = 'ON_TRNACLGROT_2AE_2AO_2AP_20260916_c266b270-886c-4d58-aff4-c68c8b7e21ee';
$doc->softwareVersion = 'MAG 1.0';
$doc->documentUuid = '00000000-0000-0000-0000-000000000001';
$doc->transportNumber = 'ТН-001';
$doc->orderNumber = '42/12';
$doc->transportDate = new DateTime();
$doc->orderDate = new DateTime();
$doc->dateFile = new DateTime();
$doc->shipment = $sodInfGO;
$doc->shipper = $svGO;
$doc->consignee = $consignee;
$doc->carrier = $carrier;
$doc->drivers = [$driver];
$doc->vehicle = $vehicle;
$doc->route = $route;
$doc->cargo = $cargo;
$doc->conditions = $conditions;
$doc->signatory = $signatory;

// >>> Ключевой блок ЭЗЗ: ссылка на подписанный электронный заказ (заявку)
$doc->orderFileId = 'ON_ZAKZVGO_2AE_2AO_20260916_c266b270-886c-4d58-aff4-c68c8b7e21ee';
$doc->orderFileDate = '16.09.2026';
$doc->orderFileTime = '10:00:00';
$doc->orderFileSignature = 'MIIB2wYJKoZIhvcNAQcCoIIBzDCCAcgCAQExDjAMBggqhQMHAQECAgUAMAsGCSqGSIb3DQEHATGCAaUwggGh...==';

$generator = new DocumentGenerator();
$xml = $generator->generate($doc);

// Валидация по актуальной XSD ФНС ЭТрН
$errors = $generator->validate($xml, __DIR__ . '/t1.xsd');
if ($errors) {
    echo "t1_with_order: НЕ ВАЛИДЕН, ошибок: " . count($errors) . PHP_EOL;
    foreach ($errors as $e) echo "  - {$e}\n";
    exit(1);
}

$generator->saveToFile($xml, __DIR__ . '/t1_with_order.xml');

// Структурная проверка: <ИдЗак> — первый потомок <СодИнфГО>.
$dom = new DOMDocument();
$dom->loadXML($xml);
$firstChild = $dom->getElementsByTagName('СодИнфГО')->item(0)?->firstElementChild;
if (!$firstChild || $firstChild->nodeName !== 'ИдЗак') {
    echo 't1_with_order: первый потомок СодИнфГО = ' . ($firstChild?->nodeName ?? '(нет)') . ', ожидался ИдЗак';
    exit(1);
}

echo 'T1 ЭТрН со ссылкой на ЭЗЗ (блок ИдЗак) успешно сформирован и валиден по XSD, сохранён в examples/t1_with_order.xml';
