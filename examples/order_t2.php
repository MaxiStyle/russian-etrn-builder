<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MaxiStyle\EtrnBuilder\DocumentGenerator;
use MaxiStyle\EtrnBuilder\Entities;
use MaxiStyle\EtrnBuilder\Enums\Order;

### ЭЗЗ Т2 — «Информация перевозчика», решение ПРИНЯТ (КНД 1110362, префикс ON_ZAKZVPER) ###

// ========================================
// ВОДИТЕЛЬ (ЭЗЗ: ИНН и ≥1 телефон обязательны)
// ========================================
$driver = new Entities\Driver();
$driver->lastName = 'Петров';
$driver->firstName = 'Петр';
$driver->middleName = 'Сидорович';
$driver->inn = '780112345678';
$driver->phones = ['+7 921 555 66 77'];
$driver->licenseSeries = '78 АБ';
$driver->licenseNumber = '123456';
$driver->licenseIssueDate = '15.06.2020';

// ========================================
// ТС + ПРИЦЕП
// ========================================
$vehicle = new Entities\Vehicle();
$vehicle->regNumber = 'А123ВС78';
$vehicle->ownershipType = Order::OWNERSHIP_OWN; // собственность
$vehicle->type = 'Грузовой тягач';
$vehicle->brand = 'КАМАЗ 5490';
$vehicle->carryingCapacity = 20.0;
$vehicle->capacity = 80.0;

$trailer = new Entities\Vehicle();
$trailer->regNumber = 'АВ1234-78';
$trailer->ownershipType = Order::OWNERSHIP_RENT; // аренда → обязателен ОснАрЛиз
$trailer->type = 'Полуприцеп-тент';
$trailer->brand = 'Schmitz';
$trailer->carryingCapacity = 20.0;
$trailer->capacity = 82.0;
$trailer->stsNumber = '78ХХ456789';
$trailer->vin = 'WSM00000005432101';

$trailerRent = new Entities\DocumentRequisites();
$trailerRent->name = 'Договор аренды прицепа';
$trailerRent->number = 'АР-101';
$trailerRent->date = new DateTime('2026-03-01');
$trailerRent->legalParticipants = ['7801234567'];
$trailer->rentLeaseDocument = $trailerRent;

// ========================================
// РАЗМЕР ПЛАТЫ И ПОРЯДОК РАСЧЕТА
// ========================================
$payment = new Entities\Payment();
$payment->currencyCode = '643';
$payment->currencyName = 'Российский рубль';
$payment->amountWithoutVat = 50000.00;
$payment->vatRate = Order::VAT_20;
$payment->amountWithVat = 60000.00;
$payment->settlement = 'Постоплата 30 календарных дней';

// ========================================
// ОТВЕТСТВЕННОЕ ЛИЦО ПЕРЕВОЗЧИКА
// ========================================
$responsible = new Entities\Person();
$responsible->lastName = 'Кузнецов';
$responsible->firstName = 'Алексей';
$responsible->middleName = 'Николаевич';
$responsible->position = 'Логист';
$responsible->phone = '+7 812 987 65 43';

// ========================================
// ПОДПИСАНТ
// ========================================
$signatory = new Entities\OrderSignatory();
$signatory->position = 'Генеральный директор';
$signatory->authorityConfirmation = Order::AUTHORITY_1;
$signatory->lastName = 'Сидоров';
$signatory->firstName = 'Семен';
$signatory->middleName = 'Семенович';

// ========================================
// СБОРКА ДОКУМЕНТА
// ========================================
$doc = new Entities\OrderT2();
$doc->fileId = 'ON_ZAKZVPER_2AO_2AE_20260917_a1b2c3d4-e5f6-7890-1234-567890abcdef';
$doc->softwareVersion = 'MAG 1.0';
$doc->dateFile = new DateTime('2026-09-17 09:30:00');
$doc->composerName = 'ООО "ПЕРЕВОЗЧИК СПБ"';

// Ссылка на подписанный Т1 (из order_t1.php)
$doc->t1FileId = 'ON_ZAKZVGO_2AE_2AO_20260916_c266b270-886c-4d58-aff4-c68c8b7e21ee';
$doc->t1FileDate = '16.09.2026';
$doc->t1FileTime = '10:00:00';
$doc->t1Signature = 'MIIB2wYJKoZIhvcNAQcCoIIBzDCCAcgCAQExDjAMBggqhQMHAQECAgUAMAsGCSqGSIb3DQEHATGCAaUwggGh...=='; // base64, stub

$doc->documentUuid = '00000000-0000-0000-0000-000000000001'; // тот же УИД_Зак, что в T1
$doc->decision = Order::DECISION_ACCEPTED; // принят
$doc->responsiblePerson = $responsible;
$doc->drivers = [$driver];
$doc->vehicle = $vehicle;
$doc->trailers = [$trailer];
$doc->payment = $payment;
$doc->signatory = $signatory;

// ========================================
// ГЕНЕРАЦИЯ, ВАЛИДАЦИЯ, СОХРАНЕНИЕ
// ========================================
$generator = new DocumentGenerator();
$generator->setCharset('windows-1251');

$xml = $generator->generate($doc);
$xsd = __DIR__ . '/order_t2.xsd';
$errors = $generator->validate($xml, $xsd);

if ($errors) {
    echo "OrderT2 (принят): НЕ ВАЛИДЕН, ошибок: " . count($errors) . PHP_EOL;
    foreach ($errors as $e) echo "  - {$e}\n";
    exit(1);
}

$generator->saveToFile($xml, __DIR__ . '/order_t2.xml');
echo 'OrderT2 (принят) успешно сформирован и валиден по XSD, сохранён в examples/order_t2.xml';
