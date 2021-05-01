<?php

/**
 * Example usage of parse-hydrodaten
 */

require __DIR__ . '/../vendor/autoload.php';

$raw = file_get_contents(__DIR__ . '/../tests/resources/validData/hydroweb_prec.xml');

$metadata = \cstuder\ParseHydrodaten\MetadataParser::parse($raw);

var_dump($metadata);
