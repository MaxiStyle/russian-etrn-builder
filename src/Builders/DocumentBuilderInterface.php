<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMDocument;
use DOMElement;

interface DocumentBuilderInterface
{
    public function build(DOMDocument $dom, DOMElement $parent, object $object): void;
}
