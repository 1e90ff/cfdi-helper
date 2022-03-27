<?php namespace _1e90ff\CfdiHelper\Utils;

use _1e90ff\CfdiHelper\Cfdi;
use _1e90ff\CfdiHelper\Exceptions\InvalidSignatureException;
use _1e90ff\CfdiHelper\Exceptions\MissingElementException;
use DateTime;
use SoapClient;

class CfdiStatusRequest
{
    public function get(Cfdi $cfdi)
    {
        $request = new SoapClient('https://consultaqr.facturaelectronica.sat.gob.mx/consultacfdiservice.svc?wsdl');

        $expresionImpresa = sprintf(
            're=%s&rr=%s&tt=%s&id=%s',
            htmlentities($cfdi->issuer()->rfc()),
            htmlentities($cfdi->receiver()->rfc()),
            $cfdi->total(),
            $cfdi->digitalTaxStamp()->uuid()
        );

        $response = $request->Consulta([
            'expresionImpresa' => $expresionImpresa
        ]);

        return $response->ConsultaResult;
    }

    public function update(Cfdi $cfdi) : Cfdi
    {
        if (!$cfdi->hasDigitalTaxStamp())
        {
            throw new MissingElementException('tfd:TimbreFiscalDigital');
        }

        if (!$cfdi->valid() || $cfdi->signature() !== $cfdi->digitalTaxStamp()->signature())
        {
            throw new InvalidSignatureException();
        }

        $now = new DateTime();

        $result = $this->get($cfdi);

        $cfdi->status(new CfdiStatus($result, $now));

        return $cfdi;
    }
}
