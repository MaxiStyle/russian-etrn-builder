<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMDocument;
use DOMElement;
use DOMException;
use MaxiStyle\EtrnBuilder\Entities;

/**
 * Билдер Титул T3 — Информация грузополучателя о приёме груза (ON_TRNACLGRPO, КНД 1110341)
 *
 * Структура:
 * Файл[@ИдФайл, @ВерсПрог, @ВерсФорм]
 *   Документ[@КНД, @ПоФактХЖ, @ДатИнфГП, @ВрИнфГП, @НаимЭконСубСост]
 *     ИдИнфПрвПрием[@ИдФайлИнфПрвПрием, @ДатФайлПрвПрием, @ВрФайлПрвПрием, @ЭП]
 *     СодИнфГП[@УИД_ТрН, @СодОпПр]
 *       ПриемГрузГП[@ФДатВрПриб, @НалКоорТочВрФПр, @ФДатВрУбыт, @НалКоорТочВрФУб,
 *                   @ЗаявДатВрПриб, @НалКоорТочВрЗПр, @МетОпрМасс, @КолМестПриемЧ,
 *                   @ОбщСвСост, @МасБрутЗначПрием]
 *         АдрВыгруз
 *           АдресИнф[@КодСтр, @АдрТекст]
 *         СвПринПоНаим[@НаимГруз, @СостГруз, @КолМест]
 *           ПерМарк
 *           МасГруз[@МасБрутЗнач]
 *     Подписант[@СтатПодп, @Должн?]
 *       ФИО[@Фамилия, @Имя, @Отчество?]
 */
class T3Builder extends DocumentBuilder implements DocumentBuilderInterface
{
    /**
     * @throws DOMException
     */
    public function build(DOMDocument $dom, DOMElement $parent, object $doc): void
    {
        if (!$doc instanceof Entities\T3) {
            throw new \InvalidArgumentException('Документ должен быть экземпляром T3');
        }

        $parent->setAttribute('ИдФайл', $doc->fileId ?? '');
        $parent->setAttribute('ВерсПрог', $doc->softwareVersion ?? '');
        $parent->setAttribute('ВерсФорм', $doc->formatVersion);

        $document = $dom->createElement('Документ');
        $document->setAttribute('КНД', $doc->knd);
        $document->setAttribute('ПоФактХЖ', $doc->documentName);
        $document->setAttribute('ДатИнфГП', $doc->dateFile->format('d.m.Y'));
        $document->setAttribute('ВрИнфГП', $doc->dateFile->format('H:i:s'));
        $document->setAttribute('НаимЭконСубСост', $doc->composerName);

        // Ссылка на файл обмена T2 (перевозчика)
        $this->appendReference(
            $dom, $document, 'ИдИнфПрвПрием',
            'ИдФайлИнфПрвПрием', 'ДатФайлПрвПрием', 'ВрФайлПрвПрием',
            $doc->t2FileId, $doc->t2FileDate, $doc->t2FileTime, $doc->t2Signature,
        );

        // Содержание — информация грузополучателя
        $sodInfGp = $dom->createElement('СодИнфГП');
        $sodInfGp->setAttribute('УИД_ТрН', $doc->documentUuid);
        $sodInfGp->setAttribute('СодОпПр', $doc->sodOper);

        // ПриемГрузГП
        $priemGp = $dom->createElement('ПриемГрузГП');
        $priemGp->setAttribute('ФДатВрПриб', $doc->actualArrival);
        $priemGp->setAttribute('НалКоорТочВрФПр', '1');
        $priemGp->setAttribute('ФДатВрУбыт', $doc->actualDeparture);
        $priemGp->setAttribute('НалКоорТочВрФУб', '1');
        $priemGp->setAttribute('ЗаявДатВрПриб', $doc->plannedArrival);
        $priemGp->setAttribute('НалКоорТочВрЗПр', '1');
        $priemGp->setAttribute('МетОпрМасс', $doc->weightDeterminationMethod);
        $priemGp->setAttribute('КолМестПриемЧ', (string)$doc->receivedUnits);
        $priemGp->setAttribute('ОбщСвСост', $doc->overallCondition);
        if ($doc->receivedGrossWeight !== null) {
            $priemGp->setAttribute('МасБрутЗначПрием', (string)$doc->receivedGrossWeight);
        }

        // АдрВыгруз
        if ($doc->unloadingAddress !== null) {
            $adr = $dom->createElement('АдрВыгруз');
            $this->appendAddressRf($dom, $adr, $doc->unloadingAddress);
            $priemGp->appendChild($adr);
        }

        // СвПринПоНаим
        $svPrin = $dom->createElement('СвПринПоНаим');
        $svPrin->setAttribute('НаимГруз', $doc->cargoName);
        $svPrin->setAttribute('СостГруз', $doc->cargoCondition);
        $svPrin->setAttribute('КолМест', (string)$doc->cargoUnits);

        $perMark = $dom->createElement('ПерМарк', $doc->cargoMarking ?: 'Отсутствует');
        $svPrin->appendChild($perMark);

        if ($doc->cargoGrossWeight !== null) {
            $masGruz = $dom->createElement('МасГруз');
            $masGruz->setAttribute('МасБрутЗнач', (string)$doc->cargoGrossWeight);
            $svPrin->appendChild($masGruz);
        }

        $priemGp->appendChild($svPrin);
        $sodInfGp->appendChild($priemGp);
        $document->appendChild($sodInfGp);

        // Подписант
        if ($doc->signatory !== null) {
            $this->appendSignatory($dom, $document, $doc->signatory);
        }

        $parent->appendChild($document);
    }
}