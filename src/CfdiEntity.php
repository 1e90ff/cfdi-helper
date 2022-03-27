<?php namespace _1e90ff\CfdiHelper;

use JsonSerializable;
use SimpleXMLElement;

abstract class CfdiEntity extends CfdiElement implements JsonSerializable
{
    private string $_rfc;

    private string $_name;

    protected function __construct(SimpleXMLElement $element)
    {
        parent::__construct($element);

        $this->_rfc = $this->getAttributeValue('Rfc', true);
        $this->_name = $this->getAttributeValue('Nombre', true);
    }

    public function rfc() : string
    {
        return $this->_rfc;
    }

    public function name() : string
    {
        return $this->_name;
    }
}
