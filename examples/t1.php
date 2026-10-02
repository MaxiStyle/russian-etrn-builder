<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MaxiStyle\EtrnBuilder\DocumentGenerator;
use MaxiStyle\EtrnBuilder\Entities;
use MaxiStyle\EtrnBuilder\Exception;

### Создаем титул Т1 ###

// ========================================
// ГРУЗООТПРАВИТЕЛЬ
// ========================================

// Контакты грузоотправителя
$contactShipper = new Entities\Contact();
$contactShipper->phone = '+7 222 888 44 44';

// Адрес грузоотправителя
$addressShipper = new Entities\Address();
$addressShipper->index = '680000';
$addressShipper->regionCode = '27';
$addressShipper->city = 'Хабаровск';
$addressShipper->street = 'Суворова';
$addressShipper->house = '82А';
$addressShipper->countryCode = '643';

// Юридическое лицо грузоотправителя
$legalEntityShipper = new Entities\LegalEntity();
$legalEntityShipper->name = 'ООО "МНОГОВОЗОФФ"';
$legalEntityShipper->inn = '2724218020';
$legalEntityShipper->kpp = '272301001';

// Создаем грузоотправителя
$svGO = new Entities\Shipper();
$svGO->status = 0; // 0 — не является экспедитором, 1 — является (тогда обязателен блок «Заказчик»)
$svGO->legalEntity = $legalEntityShipper;
$svGO->address = $addressShipper;
$svGO->contact = $contactShipper;

// СодИнфГО хранит только константу operation; сам грузоотправитель кладётся в T1::shipper
$sodInfGO = new Entities\Shipment();

// ========================================
// ГРУЗОПОЛУЧАТЕЛЬ
// ========================================

// Контакты грузополучателя
$contactConsignee = new Entities\Contact();
$contactConsignee->phone = '+7 495 123 45 67';

// Адрес грузополучателя
$addressConsignee = new Entities\Address();
$addressConsignee->index = '125009';
$addressConsignee->regionCode = '77';
$addressConsignee->city = 'Москва';
$addressConsignee->street = 'Тверская';
$addressConsignee->house = '15';
$addressConsignee->flat = '101';
$addressConsignee->countryCode = '643';

// Юридическое лицо грузополучателя
$legalEntityConsignee = new Entities\LegalEntity();
$legalEntityConsignee->name = 'ООО "ПОЛУЧАТЕЛЬ ЛТД"';
$legalEntityConsignee->inn = '7708123456';
$legalEntityConsignee->kpp = '770801001';

// Адрес доставки груза
$deliveryAddress = new Entities\Address();
$deliveryAddress->index = '143000';
$deliveryAddress->regionCode = '50';
$deliveryAddress->city = 'Одинцово';
$deliveryAddress->street = 'Ленина';
$deliveryAddress->house = '25';
$deliveryAddress->countryCode = '643';
$deliveryAddress->full = 'Московская область, г. Одинцово, ул. Ленина, д. 25';

// Создаем грузополучателя
$consignee = new Entities\Consignee();
$consignee->legalEntity = $legalEntityConsignee;
$consignee->address = $addressConsignee;
$consignee->contact = $contactConsignee;
$consignee->deliveryAddress = $deliveryAddress;

// ========================================
// ПЕРЕВОЗЧИК
// ========================================

// Контакты перевозчика
$contactCarrier = new Entities\Contact();
$contactCarrier->phone = '+7 812 987 65 43';

// Адрес перевозчика
$addressCarrier = new Entities\Address();
$addressCarrier->index = '191186';
$addressCarrier->regionCode = '78';
$addressCarrier->city = 'Санкт-Петербург';
$addressCarrier->street = 'Невский проспект';
$addressCarrier->house = '28';
$addressCarrier->countryCode = '643';

// Юридическое лицо перевозчика
$legalEntityCarrier = new Entities\LegalEntity();
$legalEntityCarrier->name = 'ООО "ПЕРЕВОЗЧИК СПБ"';
$legalEntityCarrier->inn = '7801234567';
$legalEntityCarrier->kpp = '780101001';

// Создаем перевозчика
$carrier = new Entities\Carrier();
$carrier->legalEntity = $legalEntityCarrier;
$carrier->address = $addressCarrier;
$carrier->contact = $contactCarrier;

// ========================================
// ВОДИТЕЛЬ
// ========================================

$driver = new Entities\Driver();
$driver->licenseSeries = '78 АБ';
$driver->licenseNumber = '123456';
$driver->licenseIssueDate = '15.06.2020';
$driver->phone = '+7 900 555 77 88';
$driver->lastName = 'Иванов';
$driver->firstName = 'Петр';
$driver->middleName = 'Сидорович';

// ========================================
// ТРАНСПОРТНОЕ СРЕДСТВО
// ========================================

$vehicle = new Entities\Vehicle();
$vehicle->regNumber = 'А123ВС78';
$vehicle->ownershipType = 1; // 1 - собственность
$vehicle->type = 'Грузовой автомобиль';
$vehicle->brand = 'КАМАЗ';
$vehicle->carryingCapacity = 20.0; // 20 тонн
$vehicle->capacity = 80.0; // 80 кубических метров

// ========================================
// ГРУЗ
// ========================================

$cargo = new Entities\Cargo();
$cargo->name = 'Строительные материалы';
$cargo->condition = 'Новый';
$cargo->packagingMethod = 'Навалом';
$cargo->packagingType = '00'; // без тары
$cargo->cargoUnits = 1;
$cargo->marking = 'Без маркировки';
$cargo->plannedWeight = 18500.5; // 18.5 тонн

// ========================================
// МАРШРУТ
// ========================================

// Адрес места погрузки
$loadingAddress = new Entities\Address();
$loadingAddress->index = '680000';
$loadingAddress->regionCode = '27';
$loadingAddress->city = 'Хабаровск';
$loadingAddress->street = 'Суворова';
$loadingAddress->house = '82А';
$loadingAddress->countryCode = '643';
$loadingAddress->full = 'Хабаровский край, г Хабаровск, Суворова улица 82А';

// Даты/времена в формате ДатаВремяВЗТип (25 символов: ДД.ММ.ГГГГTЧЧ:ММ:СС±ЧЧ:ММ)
$route = new Entities\Route();
$route->plannedArrival = '15.01.2024T09:00:00+10:00';
$route->actualArrival = '15.01.2024T09:15:00+10:00';
$route->actualDeparture = '15.01.2024T11:30:00+10:00';
$route->grossWeight = 18500.5;
$route->weightDeterminationMethod = '01'; // Взвешивание по общей массе
$route->cargoUnitsReceived = 1;
$route->loadingAddress = $loadingAddress;

// ========================================
// УСЛОВИЯ ПЕРЕВОЗКИ
// ========================================

$conditions = new Entities\Conditions();
$conditions->noTransshipment = '1'; // 1 — запрещена; 0 — разрешена (enum XSD)
$conditions->deliveryDateTime = '17.01.2024T18:00:00+03:00';
$conditions->redirectionPerson = 'Грузоотправитель'; // enum XSD
$conditions->redirectionMethod = 'Электронное уведомление перевозчика о переадресовке'; // enum XSD
$conditions->contactInfo = '+7 222 888 44 44';

// ========================================
// ПОДПИСАНТ
// ========================================

$signatory = new Entities\Signatory();
$signatory->status = '1'; // без доверенности
$signatory->lastName = 'Петров';
$signatory->firstName = 'Алексей';
$signatory->middleName = 'Викторович';

// ========================================
// ОСНОВНОЙ ДОКУМЕНТ
// ========================================

$doc = new Entities\T1();
$doc->fileId = 'ON_TRNACLGROT_2AE_2AO_2AP_20260916_c266b270-886c-4d58-aff4-c68c8b7e21ee';
$doc->softwareVersion = 'MAG 1.0';
$doc->documentUuid = '00000000-0000-0000-0000-000000000001'; // УИД_ТрН из пула ГИС ЭПД
$doc->transportNumber = 'ТН-001'; // Порядковый номер транспортной накладной
$doc->orderNumber = 'Без номера'; // Порядковый номер заказа (заявки)
$doc->transportDate = new DateTime(); // Дата составления транспортной накладной
$doc->orderDate = new DateTime(); // Дата заказа (заявки)
$doc->dateFile = new DateTime(); // Дата и время формирования файла обмена информации грузоотправителя
$doc->shipment = $sodInfGO; // Информация грузоотправителя (константа СодОпер)
$doc->shipper = $svGO; // Сведения о грузоотправителе (СвГО)
$doc->consignee = $consignee; // Информация грузополучателя
$doc->carrier = $carrier; // Информация о перевозчике
$doc->drivers = [$driver]; // Сведения о водителях (один или несколько СвВодит)
$doc->vehicle = $vehicle; // Информация о транспортном средстве
$doc->route = $route; // Информация о маршруте
$doc->cargo = $cargo; // Информация о грузе
$doc->conditions = $conditions; // Условия перевозки
$doc->signatory = $signatory; // Информация о подписанте

// ========================================
// ГЕНЕРАЦИЯ ДОКУМЕНТА
// ========================================

$generator = new DocumentGenerator();

try {
    $xml = $generator->generate($doc);
    $generator->saveToFile($xml, './examples/t1.xml');

    echo 'Титул 1 успешно сформирован с заполнением всех сущностей';
} catch (Exception\UnsupportedDocumentException|Exception\XMLGenerationException $e) {
    echo $e->getMessage();
}

/*
 * РАСШИРЕННЫЙ ПРИМЕР - ЗАПОЛНЕНИЕ ВСЕХ СУЩНОСТЕЙ
 *
 * В данном примере показано заполнение всех доступных сущностей документа T1:
 *
 * 1. Shipment + Shipper - информация о грузоотправителе
 * 2. Consignee - информация о грузополучателе (юридическое лицо + адрес доставки)
 * 3. Carrier - информация о перевозчике (юридическое лицо)
 * 4. Driver - информация о водителе (данные водительского удостоверения)
 * 5. Vehicle - информация о транспортном средстве (номер, тип, грузоподъемность)
 * 6. Cargo - информация о грузе (наименование, вес, упаковка)
 * 7. Route - информация о маршруте (время погрузки/разгрузки, адрес погрузки)
 * 8. Conditions - особые условия перевозки (запреты, сроки доставки)
 * 9. Signatory - информация о подписанте документа
 *
 * Все поля заполнены тестовыми данными для демонстрации возможностей библиотеки.
 *
 * АЛЬТЕРНАТИВНЫЕ ВАРИАНТЫ:
 *
 * Для Consignee и Carrier можно использовать либо LegalEntity, либо IP:
 *
 * // Пример использования IP для грузополучателя:
 * $ipConsignee = new Entities\IP();
 * $ipConsignee->fio = 'Иванов Иван Иванович';
 * $ipConsignee->inn = '123456789012';
 * $ipConsignee->ogrnip = '312123456700123';
 *
 * $consigneeIp = new Entities\Consignee();
 * $consigneeIp->ip = $ipConsignee;
 * $consigneeIp->address = $addressConsignee;
 * $consigneeIp->contact = $contactConsignee;
 * $consigneeIp->deliveryAddress = $deliveryAddress;
 *
 * // Пример использования IP для перевозчика:
 * $ipCarrier = new Entities\IP();
 * $ipCarrier->fio = 'Петров Петр Петрович';
 * $ipCarrier->inn = '987654321098';
 * $ipCarrier->ogrnip = '314987654300321';
 *
 * $carrierIp = new Entities\Carrier();
 * $carrierIp->ip = $ipCarrier;
 * $carrierIp->address = $addressCarrier;
 * $carrierIp->contact = $contactCarrier;
 */

