<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MaxiStyle\EtrnBuilder\DocumentGenerator;
use MaxiStyle\EtrnBuilder\Entities;
use MaxiStyle\EtrnBuilder\Enums\Order;

### ЭЗЗ Т2 — «Информация перевозчика», решение ОТКАЗ (КНД 1110362, префикс ON_ZAKZVPER) ###

$signatory = new Entities\OrderSignatory();
$signatory->position = 'Генеральный директор';
$signatory->authorityConfirmation = Order::AUTHORITY_1;
$signatory->lastName = 'Сидоров';
$signatory->firstName = 'Семен';
$signatory->middleName = 'Семенович';

$doc = new Entities\OrderT2();
$doc->fileId = 'ON_ZAKZVPER_2AO_2AE_20260917_deadbeef-e5f6-7890-1234-567890abcdef';
$doc->softwareVersion = 'MAG 1.0';
$doc->dateFile = new DateTime('2026-09-17 09:45:00');
$doc->composerName = 'ООО "ПЕРЕВОЗЧИК СПБ"';

// Ссылка на подписанный Т1
$doc->t1FileId = 'ON_ZAKZVGO_2AE_2AO_20260916_c266b270-886c-4d58-aff4-c68c8b7e21ee';
$doc->t1FileDate = '16.09.2026';
$doc->t1FileTime = '10:00:00';
$doc->t1Signature = 'MIIB2wYJKoZIhvcNAQcCoIIBzDCCAcgCAQExDjAMBggqhQMHAQECAgUAMAsGCSqGSIb3DQEHATGCAaUwggGh...==';

$doc->documentUuid = '00000000-0000-0000-0000-000000000001';
$doc->decision = Order::DECISION_REJECTED;
$doc->rejectReason = 'На запрошенную дату ТС занято другой перевозкой';
$doc->signatory = $signatory;

$generator = new DocumentGenerator();
$generator->setCharset('windows-1251');

$xml = $generator->generate($doc);
$xsd = __DIR__ . '/order_t2.xsd';
$errors = $generator->validate($xml, $xsd);

if ($errors) {
    echo "OrderT2 (отказ): НЕ ВАЛИДЕН, ошибок: " . count($errors) . PHP_EOL;
    foreach ($errors as $e) echo "  - {$e}\n";
    exit(1);
}

$generator->saveToFile($xml, __DIR__ . '/order_t2_reject.xml');
echo 'OrderT2 (отказ) успешно сформирован и валиден по XSD, сохранён в examples/order_t2_reject.xml';
