# parse-hydrodaten

![PHPUnit tests](https://github.com/cstuder/parse-hydrodaten/workflows/PHPUnit%20tests/badge.svg)

Simple PHP package to parse Hydrodaten (FOEN/BAFU) Open Data strings.

**Disclaimer:** This library is not official and not affiliated with FOEN/BAFU.

Created for usage on [api.existenz.ch](https://api.existenz.ch) and indirectly on [Aare.guru](https://aare.guru). As of 2020 in productive use.

## Hydrodaten

[FOEN/BAFU](https://www.bafu.admin.ch) (Swiss Federal Office for the Environment / Bundesamt für Umwelt der Schweiz) offers a selection of their [Hydrological data](https://www.hydrodaten.admin.ch) data on the [opendata.swiss portal](https://opendata.swiss/de/organization/bundesamt-fur-umwelt-bafu?keywords_de=gewasser).

One of the data formats is an XML file with the current & yesterdays water temperature, flow amount and surface height.

Not every stations measures every parameter. Not every stations reports its data at the same time.

Periodicity: 10 minutes.

**Licencing restrictions apply by FOEN/BAFU.** See the Open Data download for information. FOEN/BAFU requires that all usage of the data always labels the FOEN/BAFU as source.

### Getting the data

1. Contact the [Abfragezentrale](abfragezentrale@bafu.admin.ch) and ask for access to the file `hydroweb.xml`.
2. You should get a username and password for the endpoint <https://www.hydrodata.ch/data/xml/hydroweb.xml>.

### Data format

XML file with [associated XSD schema](https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xsd) containing a list of river measurement stations and their different parameters.

Note that this parser is only interested in the absolute measurement values (Current and 24h old). It ignores max/min/mean values.

Both data and metadata is in the same XML. Encoding is UTF-8.

```xml
<?xml version='1.0' encoding='utf-8'?>
<locations xmlns:schemaLocation="http://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" export-time="2020-07-11T14:55:36" timezone="GMT+2">
 <station number="2004" name="Murten" water-body-name="Murtensee" water-body-type="lake" easting="575500" northing="197790">
  <parameter type="2" variant="0" name="Pegel m ü. M." unit="m ü. M.">
   <datetime>2020-07-11T14:50:00</datetime>
   <value warn-level-class="1">429.44</value>
   <previous-24h>429.41</previous-24h>
   <delta-24h>0.03</delta-24h>
   <max-24h warn-level-class="1">429.45</max-24h>
   <mean-24h>429.43</mean-24h>
   <min-24h>429.41</min-24h>
   <max-1h>429.44</max-1h>
   <mean-1h>429.44</mean-1h>
   <min-1h>429.44</min-1h>
  </parameter>
 </station>

...

</locations>
```

## Installation

`composer require cstuder/parse-hydrodaten`

## Example usage

```php
<?php

require('vendor/autoload.php');

$context = stream_context_create(['http' => ['header' => 'Authorization: Basic ' . base64_encode("{$username}:{$password}")]]);
$raw = file_get_contents('https://www.hydrodata.ch/data/xml/hydroweb.xml', NULL, $context);

$data = \cstuder\ParseHydrodaten\DataParser::parse($raw);

var_dump($data);
```

## Methods

The parser is intentionally limited: It parses the given string and returns all absolute values which look valid. It silently skips over any value it doesn't understand.

Values are converted to `float`. Missing values are not returned, the values will never be `null`.

### `DataParser::parse(string $raw)`

Parses a Hydroweb XML string.

Returns an array of StdClass objects with the keys `timestamp`, `location`, `parameter`, `value`.

### `LegacyDataParser::parse(string $raw)`

Parses a legacy Hydroweb XML string from the older `SMS.xml` file, found at <https://www.hydrodata.ch/data/xml/SMS.xml>

Returns an array of StdClass objects with the keys `timestamp`, `location`, `parameter`, `value`.

### `MetadataParser::parse(string $raw)`

Parses a Hydroweb XML string.

Returns two fields: `locations` and `parameters`, both containing arrays of StdClass objects with fields such as location coordinates or parameter units.

## Testing

Run `composer test` to execute the PHPUnit test suite.

## Releasing

1. Add changes to the [changelog](CHANGELOG.md).
1. Create a new tag `vX.X.X`.
1. Push.

## License

MIT.

## Author

Christian Studer <cstuder@existenz.ch>, Bureau für digitale Existenz.
