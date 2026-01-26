<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MaxiStyle\EtrnBuilder\DocumentGenerator;
use MaxiStyle\EtrnBuilder\Entities;
use MaxiStyle\EtrnBuilder\Exception;

### Создаем титул Т1 ###

// Грузоотправитель
// Контакты
$contact = new Entities\Contact();
$contact->set('phone', '+7 222 888 44 44');

// Адрес
$address = new Entities\Address();
$address->set('full', 'Хабаровский край, г Хабаровск, Суворова улица 82А')
    ->set('countryCode', '643');
// ЮЛ
$ul = new Entities\UL();
$ul->set('name', 'ООО "МНОГОВОЗОФФ"')
    ->set('inn', '2724218020')
    ->set('kpp', '272301001');
// Основные сведенья
$svGO = new Entities\SvGO();
$svGO->set('goEksp', 0) // грузоотправитель является / не является экспедитором
    ->set('ul', $ul)
    ->set('address', $address)
    ->set('contact', $contact);
$sodInfGO = new Entities\SodInfGO();
$sodInfGO->set('svGO', $svGO);


// Основные атрибуты
$doc = new Entities\T1();
$doc->set('numTrN', 'ТН-001') // Порядковый номер транспортной накладной
->set('numOrder', 'Без номера') // Порядковый номер заказа (заявки)
->set('dateTrN', new DateTime()) // Дата составления транспортной накладной
->set('dateOrder', new DateTime()) // Дата заказа (заявки)
->set('dateFile', new DateTime()) // Дата и время формирования файла обмена информации грузоотправителя
->set('sodInfGO', $sodInfGO) // Информация грузоотправителя
;

$generator = new DocumentGenerator();

try {
    $xml = $generator->generate($doc);
    $generator->saveToFile($xml, './examples/t1.xml');

    echo 'Титул 1 успешно сформирован';
} catch (Exception\UnsupportedDocumentException|Exception\XMLGenerationException $e) {
    echo $e->getMessage();
}
