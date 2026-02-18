<?php

namespace MaxiStyle\EtrnBuilder\Entities;

use DateTimeInterface;

/**
 * Сущность Титул T1 - Информация о грузе, ТС, водителе
 */
class T1 extends Document
{
    /**
     * @var string Код документа по КНД (константа)
     */
    public string $knd = '1110339';

    /**
     * @var string ВерсФорм (константа)
     */
    public string $formatVersion = '5.01';

    /**
     * @var string Наименование документа по факту хозяйственной жизни (константа)
     */
    public string $docName = 'Транспортная накладная, информация грузоотправителя';

    /**
     * @var ?DateTimeInterface Дата и время формирования файла обмена информации грузоотправителя
     */
    public ?DateTimeInterface $dateFile = null;

    /**
     * @var ?string Идентификатор файла
     */
    public ?string $fileId = null;

    /**
     * @var ?string Версия программы, с помощью которой сформирован файл
     */
    public ?string $softwareVersion = null;

    /**
     * @var ?string UUID документа
     */
    public ?string $documentUuid = null;

    /**
     * @var ?string Содержание транспортной накладной, информация грузоотправителя
     */
    public ?Shipment $shipment = null;

    /**
     * @var ?Consignee Сведения о грузополучателе
     */
    public ?Consignee $consignee = null;

    /**
     * @var ?Carrier Сведения о перевозчике
     */
    public ?Carrier $carrier = null;

    /**
     * @var ?Shipper Сведения о грузоотправителе
     */
    public ?Shipper $shipper = null;

    /**
     * @var ?Customer Сведения о заказчике
     */
    public ?Customer $customer = null;

    /**
     * @var ?Driver Сведения о водителе
     */
    public ?Driver $driver = null;

    /**
     * @var ?Vehicle Сведения о транспортном средстве
     */
    public ?Vehicle $vehicle = null;

    /**
     * @var ?Route Сведения о передаче груза при приеме груза перевозчиком
     */
    public ?Route $route = null;

    /**
     * @var ?Cargo Сведения о грузе
     */
    public ?Cargo $cargo = null;

    /**
     * @var ?Conditions Сведения об указаниях грузоотправителя по особым условиям перевозки
     */
    public ?Conditions $conditions = null;

    /**
     * @var ?Signatory Сведения о подписанте
     */
    public ?Signatory $signatory = null;

    /**
     * @var ?string Порядковый номер транспортной накладной
     */
    public ?string $transportNumber = null;

    /**
     * @var ?DateTimeInterface Дата составления транспортной накладной
     */
    public ?DateTimeInterface $transportDate = null;

    /**
     * @var ?string Порядковый номер заказа (заявки)
     */
    public ?string $orderNumber = null;

    /**
     * @var ?DateTimeInterface Дата заказа (заявки)
     */
    public ?DateTimeInterface $orderDate = null;
}
