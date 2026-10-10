# Changelog

Все значимые изменения фиксируются здесь. Формат — [Keep a Changelog](https://keepachangelog.com/ru/1.1.0/),
версии — [SemVer](https://semver.org/lang/ru/).

## [1.7.1] — 2026-10-09

Исправление валидации XSD для `ИнфОпасн` в T1 ЭЗЗ.

### Исправлено
- `OrderT1Builder::appendDangerous` формировал неверные имена атрибутов (`НаимОтгр` вместо
  `НадОтгНаим`, `Класс` вместо `Клас`, `ГрУпак` вместо `ГрУп`, `КодОгрТун` вместо `КодОгрЧерТун`)
  и не выводил обязательные `КласКод`/`ЗнОп` — DropCat возвращал `CANT_VALIDATE_CONTENT`.
- `DangerousCargoInfo` расширен обязательными полями `classificationCode` (@КласКод) и
  `dangerSign` (@ЗнОп); оставшиеся свойства приведены в соответствие с XSD (`СвОпаснГрузТип`).
  Все семь атрибутов `ИнфОпасн` требуются одновременно, если блок формируется.

## [1.7.0] — 2026-09-16

Обратно-совместимый релиз: добавлена поддержка электронного заказа/заявки (ЭЗЗ) по формату ФНС
(приказ № ЕД-7-26/108@), общие фрагменты вынесены в базовый билдер, T1 ЭТрН научился ссылаться
на подписанный электронный заказ через блок `ИдЗак`.

### Добавлено
- **ЭЗЗ**: два новых документа
  — `OrderT1` (Информация грузоотправителя, КНД 1110361, префикс `ON_ZAKZVGO`) и
  `OrderT2` (Информация перевозчика, КНД 1110362, префикс `ON_ZAKZVPER`).
  Билдеры `OrderT1Builder`, `OrderT2Builder` формируют XML строго по `xs:sequence` XSD ФНС и
  проверяют бизнес-правила (ровно один идентификатор участника, ≥1 пункт Погрузки и Выгрузки,
  `contract` при `Функция=Заявка`, `rejectReason` при отказе, водители с ИНН и телефоном при
  принятии заказа и др.).
- **Сущности ЭЗЗ**: `OrderT1`, `OrderT2`, `Participant`, `Individual`, `ForeignEntity`,
  `SupplyPoint`, `RoutePoint`, `OrderCargo`, `DangerousCargoInfo`, `VehicleRequirements`,
  `TemperatureRegime`, `HumidityRegime`, `OrderSignatory`, `ElectronicPoa`, `PaperPoa`,
  `InfoField`, `Person`, `Payment`.
- **Enum-константы** — `MaxiStyle\EtrnBuilder\Enums\Order` (Функция, Опер, МетОпрМасс, СодОпер,
  ТипВлад, СпосПодтПолном 1..6, НалСт).
- **T1 ЭТрН → `<ИдЗак>`**: поля `T1::$orderFileId/orderFileDate/orderFileTime/orderFileSignature`
  (все `?string`). Если `orderFileId` задан, `T1Builder` выводит `<ИдЗак>` первым потомком
  `<СодИнфГО>` (по XSD `t1.xsd:131-168`); иначе XML не меняется.
- **Валидация по XSD**: `DocumentGenerator::validate(string $xml, string $xsdPath): array` —
  возвращает список ошибок `line:column: message` (пустой массив = валидно).
- **Общие helpers в `DocumentBuilder`**: `appendFio`, `appendIdentity`, `appendContact`,
  `appendInlineAddressRf`, `appendSignatory`, `appendReference`, `setAttributeIf`. `T1..T4Builder`
  переведены на них — сгенерированный XML идентичен предыдущей версии.
- **Многозначные контакты**: `Contact::$phones/$emails` (массивы) — при заполнении билдер
  выводит несколько `<Тлф>`/`<ЭлПочта>` (требование ЭЗЗ, где `Тлф` — ОМ). Одиночные
  `$phone`/`$email` сохранены как fallback для ЭТрН.
- **`Driver`**: поля `$inn: ?string`, `$phones: string[]` для ЭЗЗ.
- **Примеры**: `examples/order_t1.php`, `order_t2.php`, `order_t2_reject.php`,
  `t1_with_order.php` — каждый генерирует XML и валидирует его по XSD. Соответствующие эталонные
  файлы `order_t{1,2,2_reject}.xml`, `t1_with_order.xml`.

### Изменено
- `examples/` реорганизован: официальные XSD ФНС переименованы по титулу — `t1.xsd..t8.xsd`
  (ЭТрН T1–T8) и `order_t1.xsd`, `order_t2.xsd` (ЭЗЗ T1/T2). Каталог `examples/ezz/` удалён.
- `examples/t1.php` обновлён под актуальный формат XSD 5.01: полные адресные атрибуты
  (Индекс, КодРегион), заполнены `fileId`/`softwareVersion`/`documentUuid`, даты/времена
  приведены к 25-символьному формату `ДД.ММ.ГГГГTЧЧ:ММ:СС±ЧЧ:ММ`, значения `noTransshipment`
  и `redirection*` — по XSD-енумерациям. Файл `examples/t1.xml` проходит `validate()` без ошибок.
- `DocumentGenerator`: расширена шапка-комментарий, добавлены константы
  `DOCUMENT_TYPE_ORDER_T1/T2` и регистрация билдеров ЭЗЗ.
- `DocumentBuilder`: `appendAddressRf` (используется для АдресДостГр/ФАдресПогр/АдрВыгруз)
  и inline-вариант `appendInlineAddressRf` (для СвГО/СвЗак/СвГП/СвПер) разделены явно.

### Совместимость
Публичный API `DocumentGenerator` и билдеров `T1..T4Builder` не изменён. Все новые поля
существующих сущностей (`Contact::$phones`, `Driver::$inn`, `T1::$orderFile*` и др.) — с
безопасными дефолтами; старый пользовательский код продолжает работать.