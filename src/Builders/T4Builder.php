<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMDocument;
use DOMElement;
use DOMException;
use MaxiStyle\EtrnBuilder\Entities;

/**
 * Билдер Титул T4 — Информация перевозчика о выдаче груза грузополучателю
 * (ON_TRNACLPVYN, КНД 1110342)
 *
 * Структура:
 * Файл[@ИдФайл, @ВерсПрог, @ВерсФорм]
 *   Документ[@КНД, @ПоФактХЖ, @ДатИнфПрвВыд, @ВрИнфПрвВыд]
 *     ИдИнфГП[@ИдФайлИнфГП, @ДатФайлИнфГП, @ВрФайлИнфГП, @ЭП]
 *     СодПрвВыд[@УИД_ТрН, @СодОпер]
 *     Подписант[@СтатПодп, @Должн?]
 *       ФИО[@Фамилия, @Имя, @Отчество?]
 */
class T4Builder extends DocumentBuilder implements DocumentBuilderInterface
{
    /**
     * @throws DOMException
     */
    public function build(DOMDocument $dom, DOMElement $parent, object $doc): void
    {
        if (!$doc instanceof Entities\T4) {
            throw new \InvalidArgumentException('Документ должен быть экземпляром T4');
        }

        $parent->setAttribute('ИдФайл', $doc->fileId ?? '');
        $parent->setAttribute('ВерсПрог', $doc->softwareVersion ?? '');
        $parent->setAttribute('ВерсФорм', $doc->formatVersion);

        $document = $dom->createElement('Документ');
        $document->setAttribute('КНД', $doc->knd);
        $document->setAttribute('ПоФактХЖ', $doc->documentName);
        $document->setAttribute('ДатИнфПрвВыд', $doc->dateFile->format('d.m.Y'));
        $document->setAttribute('ВрИнфПрвВыд', $doc->dateFile->format('H:i:s'));

        // Ссылка на файл обмена T3 (грузополучателя)
        $this->appendReference(
            $dom, $document, 'ИдИнфГП',
            'ИдФайлИнфГП', 'ДатФайлИнфГП', 'ВрФайлИнфГП',
            $doc->t3FileId, $doc->t3FileDate, $doc->t3FileTime, $doc->t3Signature,
        );

        // Содержание — только UUID и текст операции (минимальный набор по XSD)
        $sodPrvVyd = $dom->createElement('СодПрвВыд');
        $sodPrvVyd->setAttribute('УИД_ТрН', $doc->documentUuid);
        $sodPrvVyd->setAttribute('СодОпер', $doc->sodOper);
        $document->appendChild($sodPrvVyd);

        // Подписант
        if ($doc->signatory !== null) {
            $this->appendSignatory($dom, $document, $doc->signatory);
        }

        $parent->appendChild($document);
    }
}
