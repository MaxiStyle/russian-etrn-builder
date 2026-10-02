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

    /**
     * @var ?string ИНН физлица (@ИННФЛ). Требуется для ЭЗЗ СвВодит; в ЭТрН — опционально.
     */
    public ?string $inn = null;

    /**
     * @var string[] Телефоны водителя (@Тлф ОМ в ЭЗЗ). Если заполнено, билдер ЭЗЗ выводит несколько
     * значений; ЭТрН T1 использует одиночный {@see $phone}.
     */
    public array $phones = [];
}