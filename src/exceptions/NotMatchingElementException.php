<?php namespace _1e90ff\CfdiHelper\Exceptions;

use Exception;

class NotMatchingElementException extends Exception
{
    public function __construct(string $actual, string $required)
    {
        parent::__construct("The actual name of the element ('$actual') does not match with '$required'.");
    }
}
