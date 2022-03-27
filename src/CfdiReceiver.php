<?php namespace _1e90ff\CfdiHelper;

use _1e90ff\CfdiHelper\Exceptions\NotMatchingElementException;
use SimpleXMLElement;

class CfdiReceiver extends CfdiEntity
{
    public function __construct(SimpleXMLElement $element)
    {
        parent::__construct($element);

        if (!$this->isElement('cfdi:Receptor'))
        {
            throw new NotMatchingElementException($element->getName(), 'cfdi:Receptor');
        }
    }

    public function jsonSerialize() : array
    {
        return [
            'rfc' => $this->rfc(),
            'name' => $this->name()
        ];
    }
}
