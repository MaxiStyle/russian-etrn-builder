<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвВодит"
 * Сведения о водителе
 */
class Driver extends BaseEntity
{
    /**
     * @var string Серия водительского удостоверения
     */
    public string $licenseSeries;

    /**
     * @var string Номер водительского удостоверения
     */
    public string $licenseNumber;

    /**
     * @var string Дата выдачи водительского удостоверения (dd.mm.yyyy)
     */
    public string $licenseIssueDate;

    /**
     * @var string Телефон водителя
     */
    public string $phone;

    /**
     * @var string Фамилия
     */
    public string $lastName;

    /**
     * @var string Имя
     */
    public string $firstName;

    /**
     * @var string Отчество
     */
    public string $middleName;
}