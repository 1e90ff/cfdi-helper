<?php namespace _1e90ff\CfdiHelper;

use _1e90ff\CfdiHelper\Exceptions\NotMatchingElementException;
use JsonSerializable;
use SimpleXMLElement;

class CfdiDigitalTaxStamp extends CfdiElement implements JsonSerializable
{
    private string $_uuid;

    private string $_date;

    private string $_signature;

    public function __construct(SimpleXMLElement $element)
    {
        parent::__construct($element);

        if (!$this->isElement('tfd:TimbreFiscalDigital'))
        {
            throw new NotMatchingElementException($element->getName(), 'tfd:TimbreFiscalDigital');
        }

        $this->_uuid = $this->getAttributeValue('UUID', true);
        $this->_date = $this->getAttributeValue('FechaTimbrado', true);
        $this->_signature = $this->getAttributeValue('SelloCFD', true);
    }

    public function uuid() : string
    {
        return $this->_uuid;
    }

    public function signature() : string
    {
        return $this->_signature;
    }

    public function date() : string
    {
        return $this->_date;
    }

    public function jsonSerialize() : array
    {
        return [
            'uuid' => $this->uuid(),
            'date' => $this->date()
        ];
    }
}
