<?php namespace _1e90ff\CfdiHelper;

use _1e90ff\CfdiHelper\Exceptions\MissingAttributeException;
use SimpleXMLElement;

abstract class CfdiElement
{
    private array $_attributes = [];

    private SimpleXMLElement $_element;

    protected function __construct(SimpleXMLElement $element)
    {
        $this->_element = $element;
        
        foreach ($this->_element->attributes() as $key => $value)
        {
            $this->_attributes[$key] = (string) $value;
        }
 
        $this->addNamespace('cfdi', 'http://www.sat.gob.mx/cfd/4');
        $this->addNamespace('tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');
    }

    public function getXml() : string
    {
        return $this->_element->asXML();
    }

    public function getElement() : SimpleXMLElement
    {
        return clone $this->_element;
    }

    public function getName() : string
    {
        return $this->_element->getName();
    }

    public function isElement(string $name) : bool
    {
        return !empty($this->getFirstElement("//$name"));
    }

    public function getAllElements(string $xpath) : array
    {
        return $this->_element->xpath($xpath);
    }

    public function getFirstElement(string $xpath) : ?SimpleXMLElement
    {
        $elements = $this->getAllElements($xpath);

        if (empty($elements))
        {
            return null;
        }

        return $elements[0];
    }

    public function hasAttribute(string $attribute) : bool
    {
        return array_key_exists($attribute, $this->_attributes);
    }

    public function getAttributeValue(string $attribute, bool $required = false) : ?string
    {
        $exists = $this->hasAttribute($attribute);

        if (!$exists && !$required)
        {
            return null;
        }

        if (!$exists && $required)
        {
            throw new MissingAttributeException($attribute, $this->getName());
        }

        return $this->_attributes[$attribute];
    }

    public function addNamespace($prefix, $namespace) : bool
    {
        return $this->_element->registerXPathNamespace($prefix, $namespace);
    }
}
