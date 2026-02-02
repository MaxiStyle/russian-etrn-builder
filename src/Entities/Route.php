<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "СвПогруз"
 * Сведения грузоотправителя о передаче груза при приеме груза перевозчиком
 */
class Route extends BaseEntity
{
    /**
     * @var string Заявленные дата и время подачи транспортного средства под погрузку
     */
    public string $plannedArrival;

    /**
     * @var string Фактические дата и время прибытия под погрузку
     */
    public string $actualArrival;

    /**
     * @var string Фактические дата и время убытия
     */
    public string $actualDeparture;

    /**
     * @var float Масса брутто груза
     */
    public float $grossWeight;

    /**
     * @var string Метод определения массы груза (01 Взвешивание по общей массе, 02 Взвешивание поосно, 03 Расчетная масса груза)
     */
    public string $weightDeterminationMethod;

    /**
     * @var int Количество грузовых мест при приеме груза перевозчиком
     */
    public int $cargoUnitsReceived;

    /**
     * @var ?Address Адрес места погрузки
     */
    public ?Address $loadingAddress;
}