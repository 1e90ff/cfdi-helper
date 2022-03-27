# cfdi-helper

A library for data extraction and validation of status and integrity of mexican CFDI.

## Usage

### Initialization

```php
<?php

use _1e90ff\CfdiHelper\Cfdi;

$cfdi = new Cfdi($element); // accepts a SimpleXMLElement object
$cfdiFromFile = Cfdi::loadFromFile('cfdi.xml');
$cfdiFromString = Cfdi::loadFromString('<?xml ...');
```

### General data access

```php
$cfdi->version();
// returns declared CFDI version (like 3.3 or 4.0)

$cfdi->valid();
// returns true or false depending on a signature integrity check success

$cfdi->type();
// returns I (Ingreso), E (Egreso), T (Traslado), P (Pago) or N (Nómina)

$cfdi->date();
// returns date of issue

$cfdi->total();
// returns total amount of the CFDI

$cfdi->currency();
// returns currency code of the total amount

$cfdi->exchangeRate();
// returns exchange rate of the currency in mexican peso (MXN)

$cfdi->signature();
// returns base64-encoded signature

$cfdi->certificate();
// returns base64-encoded certificate
```

### Issuer data access

```php
$cfdi->issuer()->rfc();
// returns RFC (Registro Federal del Contribuyente) of the issuer

$cfdi->issuer()->name();
// returns name of the issuer
```

### Receiver data access

```php
$cfdi->receiver()->rfc();
// returns RFC (Registro Federal del Contribuyente) of the receiver

$cfdi->receiver()->name();
// returns name of the receiver
```

### Digital tax stamp data access (if present)

```php
$cfdi->digitalTaxStamp()->uuid();
// returns unique identifier of the CFDI

$cfdi->digitalTaxStamp()->date();
// returns date of stamp

$cfdi->digitalTaxStamp()->signature();
// returns base64-encoded signature
```

### Getting the current CFDI status (if digital tax stamp data is present)

```php
<?php

use _1e90ff\CfdiHelper\Cfdi;
use _1e90ff\CfdiHelper\Utils\CfdiStatusRequest;

$cfdi = new Cfdi($element);

$request = new CfdiStatusRequest();

$cfdi = $request->update($cfdi);

// Helper functions

$cfdi->status->isValid();
// returns true or false depending if the CFDI is still valid for tax purposes

$cfdi->status->isCancelable();
// returns true or false depending if the CFDI is cancelable

$cfdi->status->isCancelableWithConfirmation();
// returns true or false depending if the cancelation requires receiver confirmation

$cfdi->status->isBlacklistedIssuer();
// returns true or false depending if the issuer is marked as EFOS (Empresa que Factura Operaciones Simuladas)

// Raw data

$cfdi->status->date();
// returns request date

$cfdi->status->status();
// returns Vigente (valid), Cancelado (cancelled) or No encontrado (not found)

$cfdi->status->cancelable();
// returns Cancelable (cancelable), No cancelable (not cancelable), Cancelable con aceptación (cancelable with confirmation)

$cfdi->status->issuerStatus();
// returns 100 (EFOS code) or 200 (good issuer)
```
