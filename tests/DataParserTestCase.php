<?php

namespace PHPUnit\Framework;

use cstuder\ParseValueholder\Row;
use cstuder\ParseValueholder\Value;

/**
 * Helper methods for data parser tests
 */
class DataParserTestCase extends TestCase
{
    protected function collectParameters(Row $data): array
    {
        $allParameters = array_map(function (Value $d) {
            return $d->parameter;
        }, $data->values);

        return array_unique($allParameters);
    }

    protected function collectLocations(Row $data): array
    {
        $allLocations = array_map(function (Value $d) {
            return $d->location;
        }, $data->values);

        return array_unique($allLocations);
    }

    protected function collectTimestamps(Row $data): array
    {
        $allTimestamps = array_map(function (Value $d) {
            return $d->timestamp;
        }, $data->values);

        return array_unique($allTimestamps);
    }
}
