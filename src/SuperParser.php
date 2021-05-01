<?php

namespace cstuder\ParseHydrodaten;

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
     * @return array
     */
    public static function parse(string $raw): array
    {
        // Try DataParser
        if (strpos($raw, 'hydroweb.xsd') !== false) {
            $data = DataParser::parse($raw);

            if (!empty($data)) {
                return $data;
            }
        }

        // Try DataParserPrecise
        if (strpos($raw, 'hydroweb2.xsd') !== false) {
            $data = DataParserPrecise::parse($raw);

            if (!empty($data)) {
                return $data;
            }
        }

        // Try LegacyDataParser
        $data = LegacyDataParser::parse($raw);

        if (!empty($data)) {
            return $data;
        }

        return [];
    }
}
