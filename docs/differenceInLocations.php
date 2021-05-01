<?php

/**
 * Parses both hydroweb.xml and hydroweb_prec.xml and shows the differences in locations
 */

require __DIR__ . '/../vendor/autoload.php';

$raw = file_get_contents(__DIR__ . '/../tests/resources/validData/hydroweb.xml');
$metadata = \cstuder\ParseHydrodaten\MetadataParser::parse($raw);

$raw = file_get_contents(__DIR__ . '/../tests/resources/validData/hydroweb_prec.xml');
$metadataPrec = \cstuder\ParseHydrodaten\MetadataParser::parse($raw);

$allLocations = array_map(function ($l) {
    return $l->id;
}, $metadata->locations);

$allLocationsPrec = array_map(function ($l) {
    return $l->id;
}, $metadataPrec->locations);

$diff = array_diff($allLocations, $allLocationsPrec);
sort($diff);

echo "Locations in hydroweb.xml but not in hydroweb_prec.xml:\n";

echo implode("\n", $diff);


$diff = array_diff($allLocationsPrec, $allLocations);
sort($diff);

echo "\n\nLocations in hydroweb_prec.xml but not in hydroweb.xml:\n";

echo implode("\n", $diff);
