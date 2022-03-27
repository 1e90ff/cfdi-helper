<?php namespace _1e90ff\CfdiHelper\Exceptions;

use Exception;

class MissingElementException extends Exception
{
    public function __construct(string $element)
    {
        parent::__construct("'$element' element is missing.");
    }
}
