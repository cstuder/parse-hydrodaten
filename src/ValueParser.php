<?php

namespace cstuder\ParseHydrodaten;

/**
 * Parse string values
 */
class ValueParser
{
    /**
     * NULL string as used in the XML string
     */
    protected const NULL_STRING = 'NaN';

    /**
     * Parse Wert tag
     * 
     * Recognizes empty strings and returns null.
     * Parses to float otherwise.
     * 
     * @param string $value
     * @return null|float
     */
    public static function parseValue(?string $value): ?float
    {
        if ($value === self::NULL_STRING) return null;

        if ($value === '') return null;

        if ($value === null) return null;

        return floatval($value);
    }
}
