<?php

namespace PHPUnit\Framework;

/**
 * Helper methods for data parser tests
 */
class DataParserTestCase extends TestCase
{
    protected function collectParameters($data)
    {
        $allParameters = array_map(function ($d) {
            return $d->par;
        }, $data);

        return array_unique($allParameters);
    }

    protected function collectLocations($data)
    {
        $allLocations = array_map(function ($d) {
            return $d->loc;
        }, $data);

        return array_unique($allLocations);
    }

    protected function collectTimestamps($data)
    {
        $allTimestamps = array_map(function ($d) {
            return $d->timestamp;
        }, $data);

        return array_unique($allTimestamps);
    }
}
