<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMDocument;
use DOMElement;
use DOMException;
use MaxiStyle\EtrnBuilder\Entities;
use MaxiStyle\EtrnBuilder\Entities\Driver;
use MaxiStyle\EtrnBuilder\Entities\OrderT2;
use MaxiStyle\EtrnBuilder\Entities\Payment;
use MaxiStyle\EtrnBuilder\Entities\Person;
use MaxiStyle\EtrnBuilder\Entities\Vehicle;

/**
 * Билдер файла обмена «Информация перевозчика» (ответ на ЭЗЗ, T2 ЭЗЗ).
 * КНД 1110362, префикс имени файла ON_ZAKZVПЕР.
 *
 * Наследует protected-helpers из {@see OrderT1Builder} (участник, подписант, форматтеры).
 */
class OrderT2Builder extends OrderT1Builder implements DocumentBuilderInterface
{
    /**
     * @throws DOMException
     */
    public function build(DOMDocument $dom, DOMElement $parent, object $doc): void
    {
        if (!$doc instanceof OrderT2) {
            throw new \InvalidArgumentException('Документ должен быть экземпляром OrderT2');
        }

        $this->validateT2($doc);

        // Файл
        $parent->setAttribute('ИдФайл', $doc->fileId ?? '');
        $parent->setAttribute('ВерсПрог', $doc->softwareVersion ?? '');
        $parent->setAttribute('ВерсФорм', $doc->formatVersion);
        foreach ($doc->otherRecipientIds as $id) {
            $this->append($dom, $parent, 'ИдПолИной', (string)$id);
        }

        // Документ
        $document = $dom->createElement('Документ');
        $document->setAttribute('КНД', $doc->knd);
        if ($doc->dateFile !== null) {
            $document->setAttribute('ДатИнфПрв', $doc->dateFile->format('d.m.Y'));
            $document->setAttribute('ВрИнфПрв', $doc->dateFile->format('H:i:s'));
        }
        $document->setAttribute('НаимЭкСубСост', $doc->composerName);

        if ($doc->composerBasis !== null) {
            $this->append($dom, $document, 'ОснДовОргСост', $doc->composerBasis);
        }

        // ИдИнфГО — ссылка на T1
        $this->appendReference(
            $dom, $document, 'ИдИнфГО',
            'ИдФайлИнфГО', 'ДатФайлИнфГО', 'ВрФайлИнфГО',
            $doc->t1FileId, $doc->t1FileDate, $doc->t1FileTime, $doc->t1Signature,
        );

        // СодИнфПрв
        $sodInfPrv = $dom->createElement('СодИнфПрв');
        $sodInfPrv->setAttribute('УИД_Зак', $doc->documentUuid);
        $sodInfPrv->setAttribute('СодОпер', $doc->decision);
        if ($doc->decision === '2') {
            $this->setAttributeIf($sodInfPrv, 'ПричОтк', $doc->rejectReason);
        }

        if ($doc->decision === '1') {
            if ($doc->responsiblePerson !== null) {
                $this->appendResponsiblePerson($dom, $sodInfPrv, $doc->responsiblePerson);
            }
            foreach ($doc->drivers as $driver) {
                $this->appendOrderDriver($dom, $sodInfPrv, $driver);
            }
            if ($doc->carrierPerson !== null) {
                $this->appendCarrierPerson($dom, $sodInfPrv, $doc->carrierPerson);
            }
            if ($doc->vehicle !== null || !empty($doc->trailers) || !empty($doc->specialTrafficConditions)) {
                $this->appendVehicleBlock($dom, $sodInfPrv, $doc);
            }
            if ($doc->payment !== null) {
                $this->appendPayment($dom, $sodInfPrv, $doc->payment);
            }
        }
        foreach ($doc->infoFields as $field) {
            $this->appendInfoField($dom, $sodInfPrv, $field);
        }

        $document->appendChild($sodInfPrv);

        if ($doc->signatory !== null) {
            $this->appendOrderSignatory($dom, $document, 'ПодпИнфПрв', $doc->signatory);
        }

        $parent->appendChild($document);
    }

    /**
     * Бизнес-правила T2: при отказе — обязательна причина, при принятии — ≥1 водитель с ИНН
     * и ≥1 телефоном + ТС.
     */
    protected function validateT2(OrderT2 $doc): void
    {
        if ($doc->decision === '2') {
            if (empty($doc->rejectReason)) {
                throw new \InvalidArgumentException('OrderT2: при decision=2 обязателен rejectReason (ПричОтк)');
            }
            return;
        }
        if ($doc->decision !== '1') {
            throw new \InvalidArgumentException("OrderT2: некорректное decision='{$doc->decision}', ожидается '1' или '2'");
        }
        if (empty($doc->drivers)) {
            throw new \InvalidArgumentException('OrderT2: при decision=1 требуется минимум один водитель (СвВодит)');
        }
        foreach ($doc->drivers as $i => $driver) {
            if (empty($driver->inn)) {
                throw new \InvalidArgumentException("OrderT2 [driver #{$i}]: обязателен inn (@ИННФЛ)");
            }
            if (empty($driver->phones) && empty($driver->phone)) {
                throw new \InvalidArgumentException("OrderT2 [driver #{$i}]: обязателен минимум один телефон (@Тлф ОМ)");
            }
        }
        if ($doc->vehicle === null) {
            throw new \InvalidArgumentException('OrderT2: при decision=1 требуется vehicle (СвТС/ТС)');
        }
    }

    /**
     * СвЛицОргПрвз — ответственное лицо перевозчика. По XSD ЭЗЗ T2: дочерние <Тлф>+ (ОМ, ≥1) и <ФИО>.
     * Атрибуты не поддерживаются. Если Person::$phones заполнен — используем его, иначе одиночный
     * Person::$phone (должен быть непустым).
     */
    protected function appendResponsiblePerson(DOMDocument $dom, DOMElement $parent, Person $person): void
    {
        $el = $dom->createElement('СвЛицОргПрвз');
        $this->appendPersonPhones($dom, $el, $person);
        $this->appendFio($dom, $el, $person->lastName, $person->firstName, $person->middleName);
        $parent->appendChild($el);
    }

    /**
     * СвЛицПрв — уполномоченное лицо перевозчика. По XSD: РеквДовер (РеквДокТип) + РеквЛицПрв
     * (УчастникТип). Оба обязательны при наличии блока.
     */
    protected function appendCarrierPerson(DOMDocument $dom, DOMElement $parent, Person $person): void
    {
        $el = $dom->createElement('СвЛицПрв');
        if ($person->authorityDocument !== null) {
            $this->appendRequisites($dom, $el, 'РеквДовер', $person->authorityDocument);
        }
        if ($person->participant !== null) {
            $this->appendParticipant($dom, $el, 'РеквЛицПрв', $person->participant);
        }
        $parent->appendChild($el);
    }

    /** Внутренняя: пишет один или несколько <Тлф> из Person::$phones (fallback на $phone). */
    protected function appendPersonPhones(DOMDocument $dom, DOMElement $parent, Person $person): void
    {
        // Person не имеет phones[] — используем одиночный phone. Для нескольких телефонов —
        // клиент кладёт их через несколько Person или расширяет сущность.
        if (!empty($person->phone)) {
            $this->append($dom, $parent, 'Тлф', $person->phone);
        }
    }

    protected function appendOrderDriver(DOMDocument $dom, DOMElement $parent, Driver $driver): void
    {
        $el = $dom->createElement('СвВодит');
        $this->setAttributeIf($el, 'СерВУ', $driver->licenseSeries ?? null);
        $this->setAttributeIf($el, 'НомВУ', $driver->licenseNumber ?? null);
        $this->setAttributeIf($el, 'ДатаВыдВУ', $driver->licenseIssueDate ?? null);
        $this->setAttributeIf($el, 'ИННФЛ', $driver->inn);

        // Тлф ОМ — минимум один. Если phones[] заполнены — все, иначе одиночный phone.
        if (!empty($driver->phones)) {
            foreach ($driver->phones as $phone) {
                $this->append($dom, $el, 'Тлф', (string)$phone);
            }
        } elseif (!empty($driver->phone)) {
            $this->append($dom, $el, 'Тлф', $driver->phone);
        }

        $this->appendFio(
            $dom, $el,
            $driver->lastName ?? null,
            $driver->firstName ?? null,
            !empty($driver->middleName) ? $driver->middleName : null,
        );
        $parent->appendChild($el);
    }

    /**
     * <СвТС>: <ТС> (обязателен), <Прицеп>*, <СпецУслДвиж>*.
     * Реквизиты ТС/Прицепа — как в ЭТрН T1Builder (см. XSD ЭЗЗ на предмет ОснАрЛиз для аренды).
     */
    protected function appendVehicleBlock(DOMDocument $dom, DOMElement $parent, OrderT2 $doc): void
    {
        $svTs = $dom->createElement('СвТС');

        if ($doc->vehicle !== null) {
            $this->appendVehicleElement($dom, $svTs, 'ТС', $doc->vehicle);
        }
        foreach ($doc->trailers as $trailer) {
            $this->appendVehicleElement($dom, $svTs, 'Прицеп', $trailer);
        }
        foreach ($doc->specialTrafficConditions as $cond) {
            $this->append($dom, $svTs, 'СпецУслДвиж', (string)$cond);
        }

        $parent->appendChild($svTs);
    }

    protected function appendVehicleElement(DOMDocument $dom, DOMElement $parent, string $name, Vehicle $v): void
    {
        $el = $dom->createElement($name);
        $el->setAttribute('РегНомер', $v->regNumber);
        $el->setAttribute('ТипВлад', (string)$v->ownershipType);
        $this->setAttributeIf($el, 'НомСТС', $v->stsNumber);
        $this->setAttributeIf($el, 'НомерВИН', $v->vin);

        $parTs = $dom->createElement('ПарТС');
        if (isset($v->type)) $parTs->setAttribute('Тип', $v->type);
        if (isset($v->brand)) $parTs->setAttribute('Марка', $v->brand);
        if (isset($v->carryingCapacity)) $parTs->setAttribute('Грузопод', $this->formatDecimal($v->carryingCapacity, 2));
        if (isset($v->capacity)) $parTs->setAttribute('Вместим', $this->formatDecimal($v->capacity, 2));
        $el->appendChild($parTs);

        $this->appendRentLeaseDocument($dom, $el, $v->rentLeaseDocument ?? null);

        $parent->appendChild($el);
    }

    protected function appendPayment(DOMDocument $dom, DOMElement $parent, Payment $p): void
    {
        $el = $dom->createElement('РазмПлатРасчет');
        $el->setAttribute('КодОКВ', $p->currencyCode);
        $this->setAttributeIf($el, 'ВалНаим', $p->currencyName);
        if ($p->amountWithoutVat !== null) {
            $el->setAttribute('СтТовБезНДС', $this->formatDecimal($p->amountWithoutVat, 2));
        }
        $this->setAttributeIf($el, 'НалСт', $p->vatRate);
        if ($p->amountWithVat !== null) {
            $el->setAttribute('СтТовУчНал', $this->formatDecimal($p->amountWithVat, 2));
        }
        $this->setAttributeIf($el, 'Расчет', $p->settlement);
        $parent->appendChild($el);
    }
}
