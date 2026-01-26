<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMDocument;
use DOMElement;
use DOMException;

class DocumentBuilder
{
    /**
     * @throws DOMException
     */
    public function append(DOMDocument &$dom, DOMElement &$parent, string $name, ?string $value): void
    {
        if ($value !== null) {
            $parent->appendChild($dom->createElement($name, $value));
        }
    }
}
