<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMDocument;
use DOMElement;
use DOMException;
use MaxiStyle\EtrnBuilder\Entities;

/**
 * Билдер Титул T2 — Принятие груза к перевозке перевозчиком (ON_TRNACLPPRIN, КНД 1110340)
 *
 * Структура:
 * Файл[@ИдФайл, @ВерсПрог, @ВерсФорм]
 *   Документ[@КНД, @ПоФактХЖ, @ДатИнфПрвПрием, @ВрИнфПрвПрием]
 *     ИдИнфГО[@ИдФайлИнфГО, @ДатФайлИнфГО, @ВрФайлИнфГО, @ЭП]
 *     СодИнфПрвПрием[@УИД_ТрН, @СодОпер]
 *     Подписант[@СтатПодп, @Должн?]
 *       ФИО[@Фамилия, @Имя, @Отчество?]
 */
class T2Builder extends DocumentBuilder implements DocumentBuilderInterface
{
    /**
     * @throws DOMException
     */
    public function build(DOMDocument $dom, DOMElement $parent, object $doc): void
    {
        if (!$doc instanceof Entities\T2) {
            throw new \InvalidArgumentException('Документ должен быть экземпляром T2');
        }

        $parent->setAttribute('ИдФайл', $doc->fileId ?? '');
        $parent->setAttribute('ВерсПрог', $doc->softwareVersion ?? '');
        $parent->setAttribute('ВерсФорм', $doc->formatVersion);

        $document = $dom->createElement('Документ');
        $document->setAttribute('КНД', $doc->knd);
        $document->setAttribute('ПоФактХЖ', $doc->documentName);
        $document->setAttribute('ДатИнфПрвПрием', $doc->dateFile->format('d.m.Y'));
        $document->setAttribute('ВрИнфПрвПрием', $doc->dateFile->format('H:i:s'));

        // Ссылка на файл обмена T1 (грузоотправителя)
        $idInfGO = $dom->createElement('ИдИнфГО');
        $idInfGO->setAttribute('ИдФайлИнфГО', $doc->t1FileId);
        $idInfGO->setAttribute('ДатФайлИнфГО', $doc->t1FileDate);
        $idInfGO->setAttribute('ВрФайлИнфГО', $doc->t1FileTime);
        $idInfGO->setAttribute('ЭП', $doc->t1Signature);
        $document->appendChild($idInfGO);

        // Содержание — только UUID и текст операции
        $sodInf = $dom->createElement('СодИнфПрвПрием');
        $sodInf->setAttribute('УИД_ТрН', $doc->documentUuid);
        $sodInf->setAttribute('СодОпер', $doc->sodOper);
        $document->appendChild($sodInf);

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
