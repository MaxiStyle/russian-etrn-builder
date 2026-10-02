# Russian ETRN Builder

PHP-библиотека для генерации XML-документов ФНС по электронной транспортной накладной (ЭТрН)
и электронному заказу/заявке на перевозку (ЭЗЗ). Форматы утверждены приказами ФНС России
№ ЕД-7-26/823@ и № ЕД-7-26/108@; версия формата — 5.01, кодировка `windows-1251` или `utf-8`.

## Поддерживаемые документы

### ЭТрН (транспортная накладная)

| Титул | Префикс файла  | КНД     | Кто формирует     | Билдер             |
|-------|----------------|---------|-------------------|--------------------|
| T1    | ON_TRNACLGROT  | 1110339 | Грузоотправитель  | `T1Builder`        |
| T2    | ON_TRNACLPPRIN | 1110340 | Перевозчик        | `T2Builder`        |
| T3    | ON_TRNACLGRPO  | 1110341 | Грузополучатель   | `T3Builder`        |
| T4    | ON_TRNACLPVYN  | 1110342 | Перевозчик        | `T4Builder`        |

### ЭЗЗ (электронный заказ/заявка на перевозку)

| Титул | Префикс файла | КНД     | Кто формирует    | Билдер            |
|-------|---------------|---------|------------------|-------------------|
| T1    | ON_ZAKZVGO    | 1110361 | Грузоотправитель | `OrderT1Builder`  |
| T2    | ON_ZAKZVPER   | 1110362 | Перевозчик       | `OrderT2Builder`  |

Дополнительно T1 ЭТрН поддерживает блок `ИдЗак` — ссылку на подписанный электронный заказ/заявку
(поля `T1::$orderFileId/orderFileDate/orderFileTime/orderFileSignature`).

## Требования

- PHP >= 8.1
- Расширения `ext-dom`, `ext-libxml`

## Установка

```bash
composer require maxistyle/russian-etrn-builder
```

## Быстрый старт

### T1 ЭТрН

```php
use MaxiStyle\EtrnBuilder\DocumentGenerator;
use MaxiStyle\EtrnBuilder\Entities;

$doc = new Entities\T1();
$doc->fileId = 'ON_TRNACLGROT_2AE_2AO_2AP_20260916_uuid';
$doc->softwareVersion = 'MAG 1.0';
$doc->documentUuid = '00000000-0000-0000-0000-000000000001';
$doc->dateFile = new DateTime();
// ... заполнение shipper, consignee, carrier, drivers, vehicle, route, cargo, conditions, signatory

$generator = new DocumentGenerator();
$xml = $generator->generate($doc);
$generator->saveToFile($xml, 'trn.xml');

// Полный пример: examples/t1.php
```

### T1 ЭЗЗ (заказ/заявка на перевозку)

```php
use MaxiStyle\EtrnBuilder\DocumentGenerator;
use MaxiStyle\EtrnBuilder\Entities;
use MaxiStyle\EtrnBuilder\Enums\Order;

$doc = new Entities\OrderT1();
$doc->fileId = 'ON_ZAKZVGO_2AE_2AO_20260916_uuid';
$doc->softwareVersion = 'MAG 1.0';
$doc->function = Order::FUNCTION_REQUEST; // 'Заявка' (или FUNCTION_ORDER — 'Заказ')
$doc->dateFile = new DateTime();
$doc->composerName = 'ООО "МНОГОВОЗОФФ"';
$doc->orderNumber = '42/12';
$doc->orderDate = new DateTime();
$doc->regulatoryInstructions = 'Согласно Правилам перевозок грузов автомобильным транспортом';
$doc->foodTransport = false;
// ... shipper, carrier (Participant), supplyPoint, routePoints (Погрузка+Выгрузка), cargos,
//     vehicleRequirements, signatory (OrderSignatory); при function='Заявка' — contract (DocumentRequisites)

$generator = new DocumentGenerator();
$generator->setCharset('windows-1251');
$xml = $generator->generate($doc);

// Валидация по официальной XSD ФНС
$errors = $generator->validate($xml, __DIR__ . '/examples/order_t1.xsd');
if ($errors) { /* обработать errors: массив строк "line:col: message" */ }

$generator->saveToFile($xml, 'order_t1.xml');

// Полный пример: examples/order_t1.php
```

## Структура проекта

```
russian-etrn-builder/
├── src/
│   ├── DocumentGenerator.php         # Регистрация билдеров, generate(), validate(), setCharset()
│   ├── Builders/
│   │   ├── DocumentBuilder.php       # Общие helpers: appendFio/Identity/Contact/InlineAddressRf/
│   │   │                             #                Signatory/Reference/AddressRf, setAttributeIf
│   │   ├── DocumentBuilderInterface.php
│   │   ├── T1Builder.php … T4Builder.php   # ЭТрН
│   │   ├── OrderT1Builder.php              # ЭЗЗ T1 (КНД 1110361)
│   │   └── OrderT2Builder.php              # ЭЗЗ T2 (КНД 1110362)
│   ├── Entities/                     # BaseEntity + все сущности
│   │   ├── Address.php, Contact.php, LegalEntity.php, IP.php, Individual.php,
│   │   ├── ForeignEntity.php, Signatory.php, Driver.php, Vehicle.php,
│   │   ├── DocumentRequisites.php, Cargo.php, Route.php, Conditions.php, Shipment.php,
│   │   ├── Shipper.php, Consignee.php, Carrier.php, Customer.php,
│   │   ├── T1.php … T4.php                     # ЭТрН
│   │   ├── OrderT1.php, OrderT2.php,           # ЭЗЗ документы
│   │   ├── Participant.php, SupplyPoint.php, RoutePoint.php,
│   │   ├── OrderCargo.php, DangerousCargoInfo.php,
│   │   ├── VehicleRequirements.php, TemperatureRegime.php, HumidityRegime.php,
│   │   ├── OrderSignatory.php, ElectronicPoa.php, PaperPoa.php,
│   │   ├── InfoField.php, Person.php, Payment.php
│   ├── Enums/
│   │   └── Order.php                 # Справочники ЭЗЗ (Функция/Опер/МетОпрМасс/СодОпер/ТипВлад/
│   │                                 #                СпосПодтПолном/НалСт)
│   └── Exception/
│       ├── UnsupportedDocumentException.php
│       └── XMLGenerationException.php
├── examples/
│   ├── t1.php, t1.xml                # ЭТрН T1 + эталонный XML
│   ├── t1_with_order.php, t1_with_order.xml   # T1 ЭТрН со ссылкой на ЭЗЗ (блок ИдЗак)
│   ├── order_t1.php, order_t1.xml    # ЭЗЗ T1 (Заявка)
│   ├── order_t2.php, order_t2.xml    # ЭЗЗ T2 (Принят)
│   ├── order_t2_reject.php, order_t2_reject.xml    # ЭЗЗ T2 (Отказ)
│   ├── t1.xsd … t8.xsd               # Схемы ФНС ЭТрН (T5–T8 — на будущее)
│   ├── order_t1.xsd, order_t2.xsd    # Схемы ФНС ЭЗЗ
│   └── t{1..4}.xml                   # Образцы ФНС для T2–T4 (внешние)
├── CHANGELOG.md
├── composer.json
└── README.md
```

## Основные классы

### DocumentGenerator

- `generate(object $document): string` — генерирует XML строку по сущности.
- `generateToFile(object $document, string $filename): bool`
- `saveToFile(string $xml, string $filename, bool $withBom = true): bool` — сохраняет с UTF-8 BOM.
- `setCharset(string $charset): void` — по умолчанию `utf-8`; для DropCat/1С обычно `windows-1251`.
- `validate(string $xml, string $xsdPath): array` — валидация по XSD.
  Возвращает пустой массив если валидно, иначе — список строк `line:column: message`.

### Билдеры

Все реализуют `DocumentBuilderInterface::build(DOMDocument, DOMElement $file, object $doc)`.
Общий базовый класс `DocumentBuilder` содержит утилиты, разделяемые ЭТрН и ЭЗЗ.

### Сущности

Наследники `BaseEntity` — публичные типизированные свойства с защитой от опечаток
(`__set` бросает исключение при неизвестном имени). Для ЭТрН документов —
`T1..T4`, для ЭЗЗ — `OrderT1`, `OrderT2` (в корне `Entities\` — регистрация билдеров идёт по
короткому имени класса).

### Обратная совместимость (v1.7)

- `Contact` теперь поддерживает массивы `phones[]`/`emails[]` (для ЭЗЗ, где `Тлф` — обязательное
  множественное значение). Одиночные `phone`/`email` сохранены — используются билдерами ЭТрН.
- `Driver` получил поля `inn: ?string`, `phones: string[]` (используются билдером ЭЗЗ T2; в
  ЭТрН T1 читаются, но не обязательны).
- `T1` (ЭТрН) получил поля `orderFileId/orderFileDate/orderFileTime/orderFileSignature`. Если
  задан `orderFileId`, `T1Builder` выводит блок `<ИдЗак>` первым потомком `<СодИнфГО>`; иначе
  вывод не меняется.

Публичный API T1..T4Builder и `DocumentGenerator` не изменён.

## Обработка ошибок

```php
use MaxiStyle\EtrnBuilder\Exception\UnsupportedDocumentException;
use MaxiStyle\EtrnBuilder\Exception\XMLGenerationException;

try {
    $xml = $generator->generate($doc);
} catch (UnsupportedDocumentException $e) {
    // документ не имеет зарегистрированного билдера
} catch (XMLGenerationException $e) {
    // ошибка при построении DOM
} catch (\InvalidArgumentException $e) {
    // билдер ЭЗЗ бросает при нарушении бизнес-правил формата
    // (нет пункта Погрузки/Выгрузки, нет причины при отказе, contract при Заявка и т.п.)
}
```

## Лицензия

Проект распространяется под лицензией MIT. Подробности в файле [LICENSE](LICENSE).

## Автор

**MaxiStyle**
- Email: tiranmax@yandex.ru

## Поддержка

Для получения поддержки или сообщения об ошибках создавайте issues в репозитории проекта.

---

*Библиотека разработана в соответствии с требованиями ФНС России для электронного документооборота транспортных накладных.*