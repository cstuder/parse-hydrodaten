<?php

namespace cstuder\ParseHydrodaten;

/**
 * Parser for Hydrodaten data strings in the `hydroweb2.xsd` format
 * 
 * @link https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb2.xsd
 */
class DataParserPrecise
{
    /**
     * Timezone of the timestamps in the XML string
     */
    protected const TIMEZONE = 'Europe/Zurich';

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
                $value = ValueParser::parseValue($parameter->value);
                $previousValue = ValueParser::parseValue($parameter->{'previous-24h'});

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
}
