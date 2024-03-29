# parse-hydrodaten

[![PHPUnit tests](https://github.com/cstuder/parse-hydrodaten/actions/workflows/phpunit.yml/badge.svg)](https://github.com/cstuder/parse-hydrodaten/actions/workflows/phpunit.yml)

Simple PHP package to parse Hydrodaten (FOEN/BAFU) Open Data strings.

**Disclaimer:** This library is not official and not affiliated with FOEN/BAFU.

Created for usage on [api.existenz.ch](https://api.existenz.ch) and indirectly on [Aare.guru](https://aare.guru). As of 2023 in productive use.

## Installation

`composer require cstuder/parse-hydrodaten`

## Example usage

```php
<?php
require('vendor/autoload.php');

$raw = file_get_contents('https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xml');

$data = \cstuder\ParseHydrodaten\DataParser::parse($raw);

var_dump($data);
```

The data is a row of value objects (See [cstuder/parse-valueholder](https://github.com/cstuder/parse-valueholder)):

```php
$data->values = [
  (cstuder\ParseValueholder\Value Object) [
    'timestamp' => 1619841168,
    'location' => '2135',
    'parameter' => 'temperature',
    'value' => 11.1
  ],
...
];
```

### Metadata example usage

For getting a list of locations and parameters, use the metadata parser:

```php
<?php
require('vendor/autoload.php');

$raw = file_get_contents('https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xml');

$metadata = \cstuder\ParseHydrodaten\MetadataParser::parse($raw);

var_dump($metadata);
```

## About the Hydrodaten measurement network

[FOEN/BAFU](https://www.bafu.admin.ch) (Swiss Federal Office for the Environment / Bundesamt für Umwelt der Schweiz) offers a selection of their [Hydrological data](https://www.hydrodaten.admin.ch) data on the [opendata.swiss portal](https://opendata.swiss/de/organization/bundesamt-fur-umwelt-bafu?keywords_de=gewasser).

Not every stations measures every parameter. Not every stations reports its data at the same time.

Periodicity: 10 minutes.

**Licencing restrictions apply by FOEN/BAFU.** See the Open Data download for information. FOEN/BAFU requires that all usage of the data always labels the FOEN/BAFU as source.

### Additional links

- Official homepage: <https://www.hydrodaten.admin.ch>
- [Station map on geo.admin.ch](https://map.geo.admin.ch/?lang=en&topic=ech&bgLayer=ch.swisstopo.pixelkarte-farbe&layers=ch.bafu.hydrologie-wassertemperaturmessstationen)
- [GeoJSON features](https://data.geo.admin.ch/ch.bafu.hydroweb-messstationen_temperatur/ch.bafu.hydroweb-messstationen_temperatur_en.json)
- Inofficial [Existenz data API](https://api.existenz.ch)
- Existenz data API metadata: [locations](https://api-datasette.konzept.space/existenz-api/hydro_locations) and [parameters](https://api-datasette.konzept.space/existenz-api/hydro_parameters)

## Data files overview

There are multiple ways to access the current measurement values in different precisions and different formats. Some are password protected.

| File                                 | URL                                                                                    | Format                                                         | Password protected | Temperature precision | Parseable by `parse-hydrodaten` | Comment                                                                                                                                                                                   |
| ------------------------------------ | -------------------------------------------------------------------------------------- | -------------------------------------------------------------- | ------------------ | --------------------- | ------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Homepage                             | [www.hydrodaten.admin.ch](https://www.hydrodaten.admin.ch)                             | HTML                                                           |                    | 0.1°                  |                                 |                                                                                                                                                                                           |
| `hydroweb.xml` (Rounded, deprecated) | [www.hydrodaten.admin.ch/lhg](https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xml) | [XML](http://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xsd)  |                    | 0.1°                  | Yes                             | No longer available. Contains both current values and 24h old ones.                                                                                                                |
| `hydroweb.xml` (Precise)             | [www.hydrodata.ch](https://www.hydrodata.ch/data/xml/hydroweb.xml)                     | [XML](http://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb2.xsd) | Yes                | 0.01°                 | Yes                             |                                                                                                                                                                                           |
| `hydroweb.naqua.xml`                 | [www.hydrodata.ch](https://www.hydrodata.ch/data/xml/hydroweb.naqua.xml)               | [XML](http://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb2.xsd) | Yes                | 0.01°                 | Yes                             | [NAQUA Groundwater Monitoring](https://www.bafu.admin.ch/bafu/en/home/topics/water/info-specialists/state-of-waterbodies/state-of-groundwater/naqua-national-groundwater-monitoring.html) |
| `SMS.xml`                            | -                                                                                      | XML                                                            |                    | 0.01°                 | Yes                             | No longer available                                                                                                                                                                       |

## Data file: `hydroweb.xml` (Rounded, deprecated)

**Deprecated:** No longer available as of june 2021.

Data is available without password, but rounded values (I.e. temperature values to a tenth degree.)

XML file with [associated XSD schema](https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xsd) containing a list of river measurement stations and their different parameters.

Note that this parser is only interested in the absolute measurement values (Current and 24h old). It ignores max/min/mean values.

The parser also ignores the `variant` attribute of the parameters.

Both data and metadata is in the same XML. Encoding is UTF-8. Timezone is Europe/Zurich (GMT+2).

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

### Usage data parser

1. Download the data from <https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xml>.
2. Parse it:

```php
<?php
$raw = file_get_contents('https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xml');

$data = \cstuder\ParseHydrodaten\DataParser::parse($raw);
```

## Data file: `hydroweb.xml` (Precise version)

Data is password protected, but with precise values (I.e. temperature values to a hundreth degree.)

XML file with [associated XSD schema](https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb2.xsd) containing a list of river measurement stations and their different parameters.

Note that this parser is only interested in the absolute current measurement values. It ignores max/min/mean values.

The parser also ignores the `variant` attribute of the parameters.

Both data and metadata is in the same XML. Encoding is UTF-8. Timezone is Europe/Zurich (GMT+2).

```xml
<?xml version="1.0" encoding="UTF-8"?>
<locations xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" export-time="2021-05-01T03:55:00+01:00" xsi:schemaLocation="http://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb2.xsd">
  <station name="Adelboden" easting="608710" northing="148300" number="2232" water-body-name="Allenbach" water-body-type="river">
    <parameter name="Pegel m ü. M." unit="m" field-name="BAFU_2232_PegelRadarunten">
      <datetime>2021-05-01T04:50:00+01:00</datetime>
      <value>1297.951</value>
      <max-24h>1298.026</max-24h>
      <mean-24h>1297.968</mean-24h>
      <min-24h>1297.915</min-24h>
    </parameter>
    ...
  </station>
  ...
</locations>
```

### Usage data parser precise

1. Contact the [Abfragezentrale BAFU](mailto:abfragezentrale@bafu.admin.ch) and ask for access to the file `hydroweb.xml`.
2. You should get a username and password for the endpoint <https://www.hydrodata.ch/data/xml/hydroweb.xml>.
3. Parse it:

```php
<?php
$context = stream_context_create(array (
    'http' => array (
        'header' => 'Authorization: Basic ' . base64_encode("$username:$password")
    )
));

$raw = file_get_contents('https://www.hydrodata.ch/data/xml/hydroweb.xml', $context);

$data = \cstuder\ParseHydrodaten\DataParserPrecise::parse($raw);
```

## Data file: `hydroweb.naqua.xml` (Precise version)

[Groundwater measurement network](https://www.bafu.admin.ch/bafu/en/home/topics/water/info-specialists/state-of-waterbodies/state-of-groundwater/naqua-national-groundwater-monitoring.html), same data format as `hydroweb.xml` (Precise version). Data is password protected, but with precise values (I.e. temperature values to a hundreth degree.)

### Usage data parser for NAQUA

Uses the same data parser as `hydroweb.xml` (Precise version).

1. Contact the [Abfragezentrale BAFU](mailto:abfragezentrale@bafu.admin.ch) and ask for access to the file `hydroweb.naqua.xml`.
2. You should get a username and password for the endpoint <https://www.hydrodata.ch/data/xml/hydroweb.naqua.xml>.
3. Parse it:

```php
<?php
$context = stream_context_create(array (
    'http' => array (
        'header' => 'Authorization: Basic ' . base64_encode("$username:$password")
    )
));

$raw = file_get_contents('https://www.hydrodata.ch/data/xml/hydroweb.naqua.xml', $context);

$data = \cstuder\ParseHydrodaten\DataParserPrecise::parse($raw);
```

## Legacy format: `SMS.xml`

**Deprecated:** No longer available as of april 2021.

XML file without schema containing a list of river measurement stations and their different parameters.

Note that the legacy data parser is only interested in the absolute measurement values (Current and 24h old). It ignores max/min/mean values.

The parser also ignores the `Var` attribute of the parameters.

Both data is stored in this XML, no metadata. Encoding is UTF-8. Timezone is Europe/Zurich (GMT+2).

```xml
<?xml version='1.0' encoding='utf-8'?>
<!DOCTYPE AKT_Data SYSTEM "AKT_Data.dtd">
<AKT_Data ID="SMS-Liste" ZeitSt="21.03.2021 18:25">

  <MesPar DH="HBCHa" StrNr="2304" Typ="10" Var="10">
  <Name>Ova dal Fuorn - Zernez, Punt la Drossa</Name>
    <Datum>21.03.2021</Datum>
    <Zeit>18:20</Zeit>
    <Wert>0.51</Wert>
    <Wert dt="-24h">0.51</Wert>
    <Wert Typ="delta24">-0.003</Wert>
    <Wert Typ="m24">0.51</Wert>
    <Wert Typ="max24">0.52</Wert>
    <Wert Typ="min24">0.50</Wert>
  </MesPar>

...
</AKT_Data>
```

### Usage legacy parser

```php
<?php
$raw = file_get_contents('SMS.xml');

$data = \cstuder\ParseHydrodaten\LegacyDataParser::parse($raw);
```

## Parser Methods

The parser is intentionally limited: It parses the given string and returns all absolute values which look valid. It silently skips over any value it doesn't understand.

Values are converted to `float`. Missing values are not returned, the values will never be `null`.

### `SuperParser::parse(string $raw)`

Parses a Hydrodaten data string, tries out all available parsers one after another. If any of them finds anything, returns that data.

Returns an empty row if no parsers find anything. Use at your own risk.

Returns a row of value objects with the keys `timestamp`, `loc`, `par`, `val`.

### `DataParser::parse(string $raw)`

Parses a Hydroweb XML string in the [`hydroweb.xsd`](https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xsd) format.

Returns a row of value objects with the keys `timestamp`, `loc`, `par`, `val`.

### `DataParserPrecise::parse(string $raw)`

Parses a Hydroweb XML string in the [`hydroweb2.xsd`](https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb2.xsd) format.

Returns a row of value objects with the keys `timestamp`, `loc`, `par`, `val`.

### `LegacyDataParser::parse(string $raw)`

Parses a legacy Hydroweb XML string in the deprecated `SMS.xml` format.

Returns a row of value objects with the keys `timestamp`, `loc`, `par`, `val`.

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
