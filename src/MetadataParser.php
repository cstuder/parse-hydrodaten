<?php

namespace cstuder\ParseHydrodaten;

/**
 * Parser for Hydrodaten meta data strings
 */
class MetadataParser
{
    public static function parse(string $raw)
    {
        $metadata = new \stdClass();
        $metadata->locations = [];
        $metadata->parameters = [];

        $xml = simplexml_load_string($raw);

        $allParametersByType = [];

        // Find locations
        foreach ($xml->station as $station) {
            $location = [
                'id' => $station['number'],
                'name' => $station['name'],
                'water-body-name' => $station['water-body-name'],
                'water-body-type' => $station['water-body-type'],
                'chx' => intval($station['easting']),
                'chy' => intval($station['northing']),
            ];

            $metadata->locations[] = (object) $location;

            // Gather all parameters by type
            foreach ($station->parameter as $parameter) {
                $thisParameter = [
                    'type' => (int) $parameter['type'],
                    'name' => (string) $parameter['name'],
                    'unit' => (string) $parameter['unit'],
                ];

                $allParametersByType[$thisParameter['type']] = (object) $thisParameter;
            }
        }

        // Add parameters
        $metadata->parameters = array_values($allParametersByType);

        return $metadata;
    }
}
