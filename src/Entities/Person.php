<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвЛицГО" / "СвЛицОргПрвз" / "СвЛицПрв" — уполномоченное/ответственное лицо.
 *
 * По XSD ЭЗЗ T1 (СвЛицГО): контейнер с обязательным атрибутом @Полн (1..3) и дочерними
 * РеквДокПолн (реквизиты документа о полномочиях) + РеквЛицГО (реквизиты лица, тип «УчастникТип»).
 * Для СвЛицОргПрвз / СвЛицПрв (T2) — более простой набор (ФИО, должность, телефон, email).
 * Здесь общая сущность; билдер решает, какие поля выводить в зависимости от контекста.
 */
class Person extends BaseEntity
{
    /** @var string Фамилия */
    public string $lastName = '';

    /** @var string Имя */
    public string $firstName = '';

    /** @var ?string Отчество */
    public ?string $middleName = null;

    /** @var ?string Должность */
    public ?string $position = null;

    /** @var ?string Телефон */
    public ?string $phone = null;

    /** @var ?string E-mail */
    public ?string $email = null;

    /** @var ?string Полномочия (@Полн, для СвЛицГО): 1..3 */
    public ?string $authority = null;

    /** @var ?DocumentRequisites Реквизиты документа, подтверждающего полномочия (РеквДокПолн) */
    public ?DocumentRequisites $authorityDocument = null;

    /** @var ?Participant Реквизиты лица как участника обмена (РеквЛицГО) */
    public ?Participant $participant = null;
}