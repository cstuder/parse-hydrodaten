<?php

namespace cstuder\ParseHydrodaten;

/**
 * Parser for Hydrodaten data strings
 */
class DataParser
{
    /**
     * Timezone of the timestamps in the XML string
     */
    protected const TIMEZONE = 'Europe/Zurich';

    /**
     * NULL string as used in the XML string
     */
    protected const NULL_STRING = 'NaN';

    public static function parse(string $raw)
    {
        $data = [];
        $xml = simplexml_load_string($raw);

        // Loop over stations
        foreach ($xml->station as $station) {
            // Extract values
            $loc = (int) $station['number'];

            foreach ($station->parameter as $parameter) {
                $type = (int) $parameter['type'];

                $datetime = (string) $parameter->datetime;
                $value = self::parseValue($parameter->value);
                $previousValue = self::parseValue($parameter->{'previous-24h'});

                // Validate data
                $timestamp = strtotime($datetime . ' ' . self::TIMEZONE);
                if ($timestamp == 0) {
                    continue;
                }

                // Valid value found
                if ($value !== NULL) {
                    $data[] = (object) ([
                        'timestamp' => $timestamp,
                        'loc' => $loc,
                        'par' => $type,
                        'val' => $value
                    ]);
                }

                // Old data found as well
                if ($previousValue !== NULL) {
                    $previousTimestamp = strtotime($datetime . ' ' . self::TIMEZONE . ' - 1 day');

                    $data[] = (object) ([
                        'timestamp' => $previousTimestamp,
                        'loc' => $loc,
                        'par' => $type,
                        'val' => $previousValue
                    ]);
                }
            }
        }

        return $data;
    }

    /**
     * Parse Wert tag
     * 
     * Recognizes empty strings and returns NULL.
     * Parses to float otherwise, removing thousands separators
     * 
     * @param string $value
     * @return null|float
     */
    public static function parseValue(?string $value)
    {
        if ($value === self::NULL_STRING) return null;

        if ($value === '') return null;

        if ($value === null) return null;

        return floatval($value);
    }
}
