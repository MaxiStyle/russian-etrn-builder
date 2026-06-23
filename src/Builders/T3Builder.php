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
        $idInfPrvPriem = $dom->createElement('ИдИнфПрвПрием');
        $idInfPrvPriem->setAttribute('ИдФайлИнфПрвПрием', $doc->t2FileId);
        $idInfPrvPriem->setAttribute('ДатФайлПрвПрием', $doc->t2FileDate);
        $idInfPrvPriem->setAttribute('ВрФайлПрвПрием', $doc->t2FileTime);
        $idInfPrvPriem->setAttribute('ЭП', $doc->t2Signature);
        $document->appendChild($idInfPrvPriem);

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
            $adrInf = $dom->createElement('АдресИнф');
            $adrInf->setAttribute('КодСтр', $doc->unloadingAddress->countryCode ?? '643');
            $adrInf->setAttribute('АдрТекст', $doc->unloadingAddress->full ?? '');
            $adr->appendChild($adrInf);
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
            $signatory = $doc->signatory;
            $podpis = $dom->createElement('Подписант');
            $podpis->setAttribute('СтатПодп', $signatory->status);
            if ($signatory->position !== '') {
                $podpis->setAttribute('Должн', $signatory->position);
            }
            $fio = $dom->createElement('ФИО');
            $fio->setAttribute('Фамилия', $signatory->lastName);
            $fio->setAttribute('Имя', $signatory->firstName);
            if ($signatory->middleName !== '') {
                $fio->setAttribute('Отчество', $signatory->middleName);
            }
            $podpis->appendChild($fio);
            $document->appendChild($podpis);
        }

        $parent->appendChild($document);
    }
}