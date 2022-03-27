<?php namespace _1e90ff\CfdiHelper;

use _1e90ff\CfdiHelper\Exceptions\MissingElementException;
use _1e90ff\CfdiHelper\Utils\CfdiStatus;
use InvalidArgumentException;
use JsonSerializable;
use SimpleXMLElement;

class Cfdi extends CfdiRootElement implements JsonSerializable
{
    private string $_type;

    private string $_date;

    private string $_total;

    private ?string $_currency;

    private ?string $_exchangeRate;

    private CfdiIssuer $_issuer;

    private CfdiReceiver $_receiver;

    private ?CfdiStatus $_status = null;

    public function __construct(SimpleXMLElement $element)
    {
        parent::__construct($element);

        $this->_type = $this->getAttributeValue('TipoDeComprobante', true);
        $this->_date = $this->getAttributeValue('Fecha', true);
        $this->_total = $this->getAttributeValue('Total', true);
        $this->_currency = $this->getAttributeValue('Moneda');
        $this->_exchangeRate = $this->getAttributeValue('TipoCambio');
        
        $this->mapIssuer();
        $this->mapReceiver();
    }

    private function mapIssuer() : void
    {
        $element = $this->getFirstElement('//cfdi:Comprobante/cfdi:Emisor');

        if (empty($element))
        {
            throw new MissingElementException('cfdi:Emisor');
        }

        $this->_issuer = new CfdiIssuer($element);
    }

    private function mapReceiver() : void
    {
        $element = $this->getFirstElement('//cfdi:Comprobante/cfdi:Receptor');

        if (empty($element))
        {
            throw new MissingElementException('cfdi:Receptor');
        }

        $this->_receiver = new CfdiReceiver($element);
    }

    public function type() : string
    {
        return $this->_type;
    }

    public function date() : string
    {
        return $this->_date;
    }

    public function total() : string
    {
        return $this->_total;
    }

    public function currency() : ?string
    {
        return $this->_currency;
    }

    public function exchangeRate() : ?string
    {
        return $this->_exchangeRate;
    }

    public function issuer() : CfdiIssuer
    {
        return $this->_issuer;
    }

    public function receiver() : CfdiReceiver
    {
        return $this->_receiver;
    }

    public function status(CfdiStatus $status = null) : ?CfdiStatus
    {
        if (!empty($status))
        {
            $this->_status = $status;
            
            return null;
        }

        return $this->_status;
    }

    public function jsonSerialize() : array
    {
        return [
            'status' => $this->status(),
            'valid' => $this->valid(),
            'type' => $this->type(),
            'date' => $this->date(),
            'total' => $this->total(),
            'issuer' => $this->issuer(),
            'version' => $this->version(),
            'receiver' => $this->receiver(),
            'currency' => $this->currency(),
            'exchangeRate' => $this->exchangeRate(),
            'digitalTaxStamp' => $this->digitalTaxStamp()
        ];
    }

    public static function loadFromFile(string $path) : Cfdi
    {
        if (!file_exists($path))
        {
            throw new InvalidArgumentException('File not found');
        }

        $elementString = file_get_contents($path);

        return Cfdi::loadFromString($elementString);
    }

    public static function loadFromString(string $elementString) : Cfdi
    {
        $element = new SimpleXMLElement($elementString);

        return new Cfdi($element);
    }
}
