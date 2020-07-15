<?php

/**
 * Example usage of parse-hydrodaten
 */

require __DIR__ . '/../vendor/autoload.php';

$raw = file_get_contents(__DIR__ . '/../tests/resources/validLegacyData/SMS.xml');

$data = \cstuder\ParseHydrodaten\LegacyDataParser::parse($raw);

var_dump($data);
