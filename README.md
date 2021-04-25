# parse-hydrodaten

[![PHPUnit tests](https://github.com/cstuder/parse-hydrodaten/actions/workflows/phpunit.yml/badge.svg)](https://github.com/cstuder/parse-hydrodaten/actions/workflows/phpunit.yml)

Simple PHP package to parse Hydrodaten (FOEN/BAFU) Open Data strings.

**Disclaimer:** This library is not official and not affiliated with FOEN/BAFU.

Created for usage on [api.existenz.ch](https://api.existenz.ch) and indirectly on [Aare.guru](https://aare.guru). As of 2020 in productive use.

## Hydrodaten

[FOEN/BAFU](https://www.bafu.admin.ch) (Swiss Federal Office for the Environment / Bundesamt für Umwelt der Schweiz) offers a selection of their [Hydrological data](https://www.hydrodaten.admin.ch) data on the [opendata.swiss portal](https://opendata.swiss/de/organization/bundesamt-fur-umwelt-bafu?keywords_de=gewasser).

Not every stations measures every parameter. Not every stations reports its data at the same time.

Periodicity: 10 minutes. Timezone: Europe/Zurich.

**Licencing restrictions apply by FOEN/BAFU.** See the Open Data download for information. FOEN/BAFU requires that all usage of the data always labels the FOEN/BAFU as source.

### Data files

There are multiple ways to access the current measurement values. Currently these sources are available:

| File                     | URL                                                                                    | Format                                                         | Password protected | Temperature precision | Parseable by `parse-hydroaten` | Comment                                                                                                                                                                                   |
| ------------------------ | -------------------------------------------------------------------------------------- | -------------------------------------------------------------- | ------------------ | --------------------- | ------------------------------ | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Homepage                 | [www.hydrodaten.admin.ch](https://www.hydrodaten.admin.ch)                             | HTML                                                           |                    | 0.1°                  |                                |                                                                                                                                                                                           |
| `hydroweb.xml` (Rounded) | [www.hydrodaten.admin.ch/lhg](https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xml) | [XML](http://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xsd)  |                    | 0.1°                  | Yes                            |                                                                                                                                                                                           |
| `hydroweb.xml` (Precise) | [www.hydrodata.ch](https://www.hydrodata.ch/data/xml/hydroweb.xml)                     | [XML](http://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb2.xsd) | Yes                | 0.01°                 | Yes                            |                                                                                                                                                                                           |
| `hydroweb.naqua.xml`     | [www.hydrodata.ch](https://www.hydrodata.ch/data/xml/hydroweb.naqua.xml)               | [XML](http://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb2.xsd) | Yes                | 0.01°                 | Yes                            | [NAQUA Groundwater Monitoring](https://www.bafu.admin.ch/bafu/en/home/topics/water/info-specialists/state-of-waterbodies/state-of-groundwater/naqua-national-groundwater-monitoring.html) |
| `SMS.xml`                | -                                                                                      | XML                                                            |                    | 0.01°                 | Yes                            | No longer available                                                                                                                                                                       |

### Getting the data

Station data webpage: <https://www.hydrodaten.admin.ch>

Station map on geo.admin.ch: <https://map.geo.admin.ch/?lang=en&topic=ech&bgLayer=ch.swisstopo.pixelkarte-farbe&layers=ch.bafu.hydrologie-wassertemperaturmessstationen>

GeoJSON features: <https://data.geo.admin.ch/ch.bafu.hydroweb-messstationen_temperatur/ch.bafu.hydroweb-messstationen_temperatur_en.json>

#### `hydroweb.xml` (Rounded version)

Data is available without password, but rounded values (I.e. temperature values to a tenth degree.)

1. Download the data from <https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xml>.

#### `hydroweb.xml` (Precise version)

**Note:** This library can not yet parse the precise `hydroweb.xml`.

Data is password protected, but with precise values (I.e. temperature values to a thousandths degree.)

1. Contact the [Abfragezentrale](mailto:abfragezentrale@bafu.admin.ch) and ask for access to the file `hydroweb.xml`.
2. You should get a username and password for the endpoint <https://www.hydrodata.ch/data/xml/hydroweb.xml>.

#### Legacy format: `SMS.xml`

_Deprecated:_ No longer available as of april 2021.

1. Contact the [Abfragezentrale](mailto:abfragezentrale@bafu.admin.ch) and ask for access to the file `SMS.xml`.
2. You should get a username and password for the endpoint <https://www.hydrodata.ch/data/xml/SMS.xml>.

### Data format: `hydroweb.xml`

XML file with [associated XSD schema](https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xsd) containing a list of river measurement stations and their different parameters.

Note that this parser is only interested in the absolute measurement values (Current and 24h old). It ignores max/min/mean values.

The parser also ignores the `variant` attribute of the parameters.

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

#### Legacy data format: `SMS.xml`

XML file without schema containing a list of river measurement stations and their different parameters.

Note that the legacy data parser is only interested in the absolute measurement values (Current and 24h old). It ignores max/min/mean values.

The parser also ignores the `Var` attribute of the parameters.

Both data is stored in this XML, no metadata. Encoding is UTF-8.

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

## Methods

The parser is intentionally limited: It parses the given string and returns all absolute values which look valid. It silently skips over any value it doesn't understand.

Values are converted to `float`. Missing values are not returned, the values will never be `null`.

### `SuperParser::parse(string $raw)`

Parses a Hydrodaten data string, tries out all available parsers one after another. If any of them finds anything, returns that data.

Returns an empty array if no parsers find anything. Use at your own risk.

Returns an array of StdClass objects with the keys `timestamp`, `loc`, `par`, `val`.

### `DataParser::parse(string $raw)`

Parses a Hydroweb XML string.

Returns an array of StdClass objects with the keys `timestamp`, `loc`, `par`, `val`.

### `LegacyDataParser::parse(string $raw)`

Parses a legacy Hydroweb XML string from the older `SMS.xml` file, found at <https://www.hydrodata.ch/data/xml/SMS.xml>

Returns an array of StdClass objects with the keys `timestamp`, `loc`, `par`, `val`.

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
