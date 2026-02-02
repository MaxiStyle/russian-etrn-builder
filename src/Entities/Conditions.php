<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "УказГО"
 * Сведения об указаниях грузоотправителя по особым условиям перевозки
 */
class Conditions extends BaseEntity
{
    /**
     * @var string Запрещение перегрузки груза
     */
    public string $noTransshipment;

    /**
     * @var string Дата и время доставки груза
     */
    public string $deliveryDateTime;

    /**
     * @var string Лицо, по указанию которого может осуществляться переадресовка
     */
    public string $redirectionPerson;

    /**
     * @var string Способ передачи указания на переадресовку
     */
    public string $redirectionMethod;

    /**
     * @var string Контакты лица, по указанию которого может осуществляться перевозка
     */
    public string $contactInfo;
}