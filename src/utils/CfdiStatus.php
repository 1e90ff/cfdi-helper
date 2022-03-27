<?php namespace _1e90ff\CfdiHelper\Utils;

use DateTime;
use JsonSerializable;

class CfdiStatus implements JsonSerializable
{
    private DateTime $_date;

    private string $_status;

    private string $_cancelable;

    private int $_issuerStatus;

    public function __construct($consultaResult, DateTime $date)
    {
        $this->_date = $date;
        $this->_status = $consultaResult->Estado;
        $this->_cancelable = $consultaResult->EsCancelable;
        $this->_issuerStatus = (int) $consultaResult->ValidacionEFOS;
    }

    public function isValid() : bool
    {
        return $this->_status === 'Vigente';
    }

    public function isCancelable() : bool
    {
        return $this->_cancelable !== 'No cancelable';
    }

    public function isCancelableWithConfirmation() : bool
    {
        return $this->_cancelable === 'Cancelable con aceptación';
    }

    public function isBlacklistedIssuer() : bool
    {
        return $this->_issuerStatus === 100;
    }

    public function status() : string
    {
        return $this->_status;
    }

    public function cancelable() : string
    {
        return $this->_cancelable;
    }

    public function issuerStatus() : string
    {
        return $this->_issuerStatus;
    }

    public function date() : string
    {
        return $this->_date->format('Y-m-d\TH:i:s');
    }

    public function jsonSerialize() : array
    {
        return [
            'date' => $this->date(),
            'valid' => $this->isValid(),
            'cancelable' => $this->isCancelable(),
            'cancelableWithConfirmation' => $this->isCancelableWithConfirmation(),
            'blacklistedIssuer' => $this->isBlacklistedIssuer()
        ];
    }
}
