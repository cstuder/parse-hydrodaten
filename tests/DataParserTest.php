<?php

use PHPUnit\Framework\TestCase;

/**
 * Quantitative tests of data parsers
 */
class DataParserTest extends TestCase
{
    public function testDataParser()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb.xml');
        $data = \cstuder\ParseHydrodaten\DataParser::parse($raw);

        $this->assertEquals((504 - 3) * 2, count($data));
        $this->assertEquals(5, count($this->collectParameters($data)));
        $this->assertEquals(238, count($this->collectLocations($data)));
        $this->assertEquals(34, count($this->collectTimestamps($data)));
    }


    public function testLegacyDataParser()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validLegacyData/SMS.xml');
        $data = \cstuder\ParseHydrodaten\LegacyDataParser::parse($raw);

        $this->assertEquals((504 - 3) * 2, count($data));
        $this->assertEquals(5, count($this->collectParameters($data)));
        $this->assertEquals(238, count($this->collectLocations($data)));
        $this->assertEquals(34, count($this->collectTimestamps($data)));
    }

    private function collectParameters($data)
    {
        $allParameters = array_map(function ($d) {
            return $d->par;
        }, $data);

        return array_unique($allParameters);
    }

    private function collectLocations($data)
    {
        $allLocations = array_map(function ($d) {
            return $d->loc;
        }, $data);

        return array_unique($allLocations);
    }

    private function collectTimestamps($data)
    {
        $allTimestamps = array_map(function ($d) {
            return $d->timestamp;
        }, $data);

        return array_unique($allTimestamps);
    }
}
