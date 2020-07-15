<?php

namespace cstuder\ParseHydrodaten;

/**
 * Parser for legacy Hydrodaten data strings
 *
 */
class LegacyDataParser
{
    /**
     * Timezone of the timestamps in the XML string
     */
    protected const TIMEZONE = 'Europe/Zurich';

    /**
     * Parse data string
     * 
     * @param string $raw legacy Hydrodaten data string
     * @return array
     */
    public static function parse(string $raw)
    {
        $data = [];
        $xml = simplexml_load_string($raw);

        // Loop over stations
        foreach ($xml->MesPar as $station) {
            // Extract values
            $loc = (int) $station['StrNr'];
            $type = (int) $station['Typ'];

            $datetime = ((string) $station->Datum) . ' ' . ((string) $station->Zeit) . ':00';

            $values = $station->Wert;
            $value = NULL;
            $previousValue = NULL;

            foreach ($values as $v) {
                $attributes = $v->attributes();

                if (count($attributes)) {
                    if (isset($v['dt'])) {
                        $previousValue = self::parseWert($v);
                    }
                } else {
                    // The node without attributes is the value we're looking for
                    $value = self::parseWert($v);
                }
            }

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
    public static function parseWert(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            return NULL;
        }

        $value = str_replace("'", '', $value);

        return floatval($value);
    }
}
