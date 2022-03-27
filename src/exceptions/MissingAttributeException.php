<?php namespace _1e90ff\CfdiHelper\Exceptions;

use Exception;

class MissingAttributeException extends Exception
{
    public function __construct(string $attribute, string $element)
    {
        parent::__construct("'$attribute' attribute from '$element' element is missing.");
    }
}
