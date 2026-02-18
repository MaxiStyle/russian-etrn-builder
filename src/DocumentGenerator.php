<?php

namespace MaxiStyle\EtrnBuilder;

use DOMDocument;
use DOMElement;
use DOMException;
use Exception;
use MaxiStyle\EtrnBuilder\Builders\T1Builder;
use MaxiStyle\EtrnBuilder\Exception\UnsupportedDocumentException;
use MaxiStyle\EtrnBuilder\Exception\XMLGenerationException;

/**
 * Основной класс для генерации всего XML документа в формате ФНС
 * Префикс          Титул   Кто формирует       Назначение
 * ON_TRNACLGROT    Т1      Грузоотправитель    Информация о грузе, ТС, водителе
 * ON_TRNACLPPRIN   Т2      Перевозчик          Принятие груза к перевозке
 * ON_TRNACLGRPO    Т3      Грузополучатель     Приемка груза, состояние при выгрузке
 * ON_TRNACLPVYN    Т4      Перевозчик          Выдача груза, замечания по выгрузке
 * --доп титулы----------------------------------------------------------------------
 * ON_TRNPUDPER     Т5      Перевозчик          Перевозчик фиксирует стоимость услуг и НДС (для бухучета).
 * ON_TRNPUDGO      Т6      Грузоотправитель    Грузоотправитель подтверждает Т5
 * ON_TRNPEREADR    Т7      Перевозчик          Переадресация груза
 * ON_TRNZAMEN      Т8      Перевозчик          Замена ТС/водителя
 */
class DocumentGenerator
{
    /** @var string Кодировка XML */
    private string $charset = 'utf-8';

    /** @var array Массив билдеров */
    private array $builders = [];

    /** @var array Массив кодов титулов  */
    private array $codeIds = [];

    public const string DOCUMENT_TYPE_T1 = 'T1';
    public const string DOCUMENT_TYPE_T2 = 'T2';
    public const string DOCUMENT_TYPE_T3 = 'T3';
    public const string DOCUMENT_TYPE_T4 = 'T4';


    public function __construct(string $version = '1.6', string $format = null)
    {
        // Register builders
        $this->builders[self::DOCUMENT_TYPE_T1] = new T1Builder();
        //$this->builders[self::DOCUMENT_TYPE_T2] = new T2Builder();
        //$this->builders[self::DOCUMENT_TYPE_T3] = new T3Builder();
        //$this->builders[self::DOCUMENT_TYPE_T4] = new T4Builder();

        // Register id's code
        $this->codeIds[self::DOCUMENT_TYPE_T1] = 'ON_TRNACLGROT';
        $this->codeIds[self::DOCUMENT_TYPE_T2] = 'ON_TRNACLPPRIN';
        $this->codeIds[self::DOCUMENT_TYPE_T3] = 'ON_TRNACLGRPO';
        $this->codeIds[self::DOCUMENT_TYPE_T4] = 'ON_TRNACLPVYN';

    }

    /**
     * Generate XML for document
     *
     * @param object $document Single document or array of documents
     * @return string
     * @throws UnsupportedDocumentException
     * @throws XMLGenerationException
     */
    public function generate(object $document): string
    {
        // Validate all documents have registered builders
        $documentType = $this->getDocumentType($document);
        if (!isset($this->builders[$documentType])) {
            throw new UnsupportedDocumentException("No builder registered for document type: {$documentType}");
        }

        try {
            $dom = new DOMDocument('1.0', $this->charset);
            $dom->xmlStandalone = true; // Set standalone to true
            $dom->formatOutput = true;

            // вклеиваем документ в файл
            $file = $dom->createElement('Файл');
            $documentType = $this->getDocumentType($document);
            $builder = $this->builders[$documentType];
            $builder->build($dom, $file, $document);

            $dom->appendChild($file);

            $xml = $dom->saveXML();
            if ($xml === false) {
                throw new XMLGenerationException('Failed to generate XML string');
            }
            return $xml;
        } catch (Exception $e) {
            throw new XMLGenerationException('XML generation failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Save XML to file with BOM for correct Cyrillic display in 1C
     */
    public function saveToFile(string $xml, string $filename, bool $withBom = true): bool
    {
        if ($withBom) {
            $bom = pack('H*', 'EFBBBF');
            $xml = $bom . $xml;
        }

        return file_put_contents($filename, $xml) !== false;
    }

    /**
     * Generate and save document(s) to file
     * @param object $document
     * @param string $filename
     * @return bool
     * @throws UnsupportedDocumentException
     * @throws XMLGenerationException
     */
    public function generateToFile(object $document, string $filename): bool
    {
        $xml = $this->generate($document);
        return $this->saveToFile($xml, $filename);
    }

    /**
     * По классу объекта сущности определяем билдер
     * @param object $document
     * @return string
     */
    private function getDocumentType(object $document): string
    {
        $class = get_class($document);
        $class = substr($class, strrpos($class, '\\') + 1);
        return $class; // Возвращаем в исходном регистре
    }

    /**
     * @param string $charset
     * @return void
     */
    public function setCharset(string $charset): void
    {
        $this->charset = $charset;
    }

    /**
     * По классу объекта сущности определяем код ID
     * @param object $document
     * @return string
     */
    private function getCodeId(object $document): string
    {
        return $this->codeIds[$this->getDocumentType($document)];
    }

    /**
     * @throws DOMException
     */
    protected function createElement(DOMDocument $dom, DOMElement $parent, string $name, string $value): DOMElement
    {
        $element = $dom->createElement(
            $name,
            htmlspecialchars($value, ENT_XML1, 'UTF-8')
        );
        $parent->appendChild($element);
        return $element;
    }
}
