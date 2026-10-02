<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "ПодпИнфГО" / "ПодпИнфПрв" — подписант ЭЗЗ.
 * Отличается от {@see Signatory} ЭТрН (у последнего @СтатПодп с иным набором значений).
 *
 * По XSD: @Должн (О), @ТипПодпис? (1/2/3), @СпосПодтПолном (1..6, О), @ДопСведПодп?;
 * дочерние ФИО[@Фамилия, @Имя, @Отчество?], СвДоверЭл* (при СпосПодтПолном=3), СвДоверБум* (при =5).
 */
class OrderSignatory extends BaseEntity
{
    /** @var string Должность (@Должн) */
    public string $position = '';

    /** @var ?string Тип подписи (@ТипПодпис): '1'/'2'/'3' */
    public ?string $signatureType = null;

    /** @var string Способ подтверждения полномочий (@СпосПодтПолном): '1'..'6' */
    public string $authorityConfirmation = '';

    /** @var ?string Дополнительные сведения о подписанте (@ДопСведПодп) */
    public ?string $additionalInfo = null;

    /** @var string Фамилия (ФИО@Фамилия) */
    public string $lastName = '';

    /** @var string Имя (ФИО@Имя) */
    public string $firstName = '';

    /** @var ?string Отчество (ФИО@Отчество) */
    public ?string $middleName = null;

    /** @var ElectronicPoa[] МЧД (СвДоверЭл*), обязателен при СпосПодтПолном=3 */
    public array $electronicPoa = [];

    /** @var PaperPoa[] Бумажные доверенности (СвДоверБум*), обязателен при СпосПодтПолном=5 */
    public array $paperPoa = [];
}