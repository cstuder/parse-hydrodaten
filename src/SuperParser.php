<?php

namespace cstuder\ParseHydrodaten;

use cstuder\ParseValueholder\Row;

/**
 * Super Parser for Hydrodaten data strings
 */
class SuperParser
{
    /**
     * Parse data string
     * 
     * Tries multiple different parsers.
     * 
     * Fails silently when nothing is found or understood. Use with caution.
     * 
     * @param string $raw Hydrodaten data string
     * @return Row Parsed data
     */
    public static function parse(string $raw): Row
    {
        // Try DataParser
        if (strpos($raw, 'hydroweb.xsd') !== false) {
            $data = DataParser::parse($raw);

            if (!empty($data->values)) {
                return $data;
            }
        }

        // Try DataParserPrecise
        if (strpos($raw, 'hydroweb2.xsd') !== false) {
            $data = DataParserPrecise::parse($raw);

            if (!empty($data->values)) {
                return $data;
            }
        }

        // Try LegacyDataParser
        $data = LegacyDataParser::parse($raw);

        if (!empty($data->values)) {
            return $data;
        }

        return new Row();
    }
}
