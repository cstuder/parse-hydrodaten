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

        $allParametersByKey = [];

        // Find locations
        foreach ($xml->station as $station) {
            $location = [
                'id' => trim((string) $station['number']),
                'name' => trim((string) $station['name']),
                'water-body-name' => trim((string) $station['water-body-name']),
                'water-body-type' => trim((string) $station['water-body-type']),
                'chx' => intval($station['easting']),
                'chy' => intval($station['northing']),
            ];

            $metadata->locations[] = (object) $location;

            // Gather all parameters by key
            foreach ($station->parameter as $parameter) {
                // `hydroweb2.xsd` stores the parameter identifier in the attribute `name`
                $key = trim((string) $parameter['name']);

                $thisParameter = [
                    'name' => trim((string) $parameter['name']),
                    'unit' => trim((string) $parameter['unit']),
                ];

                // `hydroweb.xsd` stores the parameter identifier in the attribute `type`
                if (isset($parameter['type'])) {
                    $key = (int) $parameter['type'];
                    $thisParameter['type'] = (int) $parameter['type'];
                }

                $allParametersByKey[$key] = (object) $thisParameter;
            }
        }

        // Add unique parameters
        $metadata->parameters = array_values($allParametersByKey);

        return $metadata;
    }
}
