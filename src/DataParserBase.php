<?php

namespace cstuder\ParseHydrodaten;

use cstuder\ParseValueholder\Row;
use cstuder\ParseValueholder\Value;

/**
 * Base class for the data parser
 * 
 * @link https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb.xsd
 * @link https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb2.xsd
 */
abstract class DataParserBase
{
    /**
     * Timezone of the timestamps in the XML string
     */
    protected const TIMEZONE = 'Europe/Zurich';

    /**
     * Parameter identifier attribute
     */
    protected const PARAMETER_ID_ATTRIBUTE = 'type';

    /**
     * Parse data string
     * 
     * @param string $raw Raw data string
     * @return Row Parsed data
     */
    public static function parse(string $raw) : Row
    {
        $data = new Row();
        $xml = simplexml_load_string($raw);

        // Loop over stations
        foreach ($xml->station as $station) {
            // Extract values
            $loc = trim((string) $station['number']);

            foreach ($station->parameter as $parameter) {
                $type = trim((string) $parameter[static::PARAMETER_ID_ATTRIBUTE]);

                $datetime = (string) $parameter->datetime;
                $value = ValueParser::parseValue($parameter->value);
                $previousValue = ValueParser::parseValue($parameter->{'previous-24h'});

                // Validate data
                $timestamp = strtotime($datetime . ' ' . static::TIMEZONE);
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
                    $previousTimestamp = strtotime($datetime . ' ' . static::TIMEZONE . ' - 1 day');

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
        }

        return $data;
    }
}
