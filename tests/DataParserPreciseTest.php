<?php

use PHPUnit\Framework\TestCase;

/**
 * Quantitative tests of data parsers
 */
class DataParserPreciseTest extends TestCase
{
    public function testDataParserPrecise()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb_prec.xml');
        $data = \cstuder\ParseHydrodaten\DataParserPrecise::parse($raw);

        $this->assertEquals(477, $data->getCount());
        $this->assertEquals(5, count($data->getParameters()));
        $this->assertEquals(226 - 6, count($data->getLocations())); // 6 locations do not deliver data
        $this->assertEquals(5, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }

    public function testDataParserPreciseNAQUA()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb.naqua.xml');
        $data = \cstuder\ParseHydrodaten\DataParserPrecise::parse($raw);

        $this->assertEquals(121, $data->getCount());
        $this->assertEquals(6, count($data->getParameters()));
        $this->assertEquals(79 - 20, count($data->getLocations())); // 20 locations do not deliver data
        $this->assertEquals(7, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }
}
