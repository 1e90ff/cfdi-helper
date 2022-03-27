<?php namespace _1e90ff\CfdiHelper;

use _1e90ff\CfdiHelper\Exceptions\NotMatchingElementException;
use DOMDocument;
use Genkgo\Xsl\Cache\NullCache;
use Genkgo\Xsl\XsltProcessor;
use SimpleXMLElement;

abstract class CfdiRootElement extends CfdiElement
{
    private bool $_valid;

    private string $_version;

    private string $_signature;

    private string $_certificate;

    private string $_originalString;

    private ?CfdiDigitalTaxStamp $_digitalTaxStamp;

    protected function __construct(SimpleXMLElement $element)
    {
        parent::__construct($element);

        if (!$this->isElement('cfdi:Comprobante'))
        {
            throw new NotMatchingElementException($element->getName(), 'cfdi:Comprobante');
        }

        $this->_version = $this->getAttributeValue('Version', true);
        $this->_signature = $this->getAttributeValue('Sello', true);
        $this->_certificate = $this->getAttributeValue('Certificado', true);
        $this->_originalString = $this->transformToOriginalString();
        
        $this->mapDigitalTaxStamp();
        
        $this->_valid = $this->checkSignature();
    }

    private function mapDigitalTaxStamp() : void
    {
        $element = $this->getFirstElement('//cfdi:Comprobante/cfdi:Complemento/tfd:TimbreFiscalDigital');

        if (empty($element))
        {
            $this->_digitalTaxStamp = null;
            return;
        }

        $this->_digitalTaxStamp = new CfdiDigitalTaxStamp($element);
    }

    private function transformToOriginalString() : ?string
    {
        $version = explode('.', $this->_version);

        $xsl = new DOMDocument();
        $xsl->load('resources/cfdi' . implode('', $version) . '/cadenaoriginal_' . implode('_', $version) . '.xslt');

        $transpiler = new XsltProcessor(new NullCache());
        $transpiler->importStyleSheet($xsl);

        return $transpiler->transformToXML($this->getElement());
    }

    private function checkSignature() : bool
    {
        $cert = "-----BEGIN CERTIFICATE-----\n" . chunk_split($this->_certificate, 64) . "-----END CERTIFICATE-----\n";
        
        $pubKey = openssl_get_publickey(openssl_x509_read($cert));

        return openssl_verify($this->_originalString, base64_decode($this->_signature), $pubKey, OPENSSL_ALGO_SHA256) === 1;
    }

    public function version() : string
    {
        return $this->_version;
    }

    public function signature() : string
    {
        return $this->_signature;
    }

    public function certificate() : string
    {
        return $this->_certificate;
    }

    public function digitalTaxStamp() : ?CfdiDigitalTaxStamp
    {
        return $this->_digitalTaxStamp;
    }

    public function hasDigitalTaxStamp() : bool
    {
        return !empty($this->_digitalTaxStamp);
    }

    public function originalString() : ?string
    {
        return $this->_originalString;
    }

    public function valid() : bool
    {
        return $this->_valid;
    }
}
