<?php

namespace cstuder\ParseHydrodaten;

use cstuder\ParseValueholder\Row;
use cstuder\ParseValueholder\Value;

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
     * @param string $raw Legacy Hydrodaten data string
     * @return Row Parse data
     */
    public static function parse(string $raw): Row
    {
        $data = new Row();
        $xml = simplexml_load_string($raw);

        // Loop over stations
        foreach ($xml->MesPar as $station) {
            // Extract values
            $loc = (int) $station['StrNr'];
            $type = (int) $station['Typ'];

            $datetime = ((string) $station->Datum) . ' ' . ((string) $station->Zeit) . ':00';

            $values = $station->Wert;
            $value = null;
            $previousValue = null;

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
            if ($value !== null) {
                $data->append(
                    new Value(
                        $timestamp,
                        $loc,
                        $type,
                        $value
                    )
                );
            }

            // Old data found as well
            if ($previousValue !== null) {
                $previousTimestamp = strtotime($datetime . ' ' . self::TIMEZONE . ' - 1 day');

                $data->append(
                    new Value(
                        $previousTimestamp,
                        $loc,
                        $type,
                        $previousValue
                    )
                );
            }
        }

        return $data;
    }

    /**
     * Parse Wert tag
     * 
     * Recognizes empty strings and returns null.
     * Parses to float otherwise, removing thousands separators
     * 
     * @param string $value
     * @return null|float
     */
    public static function parseWert(string $value): ?float
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $value = str_replace("'", '', $value);

        return floatval($value);
    }
}
