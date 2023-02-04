<?php

use PHPUnit\Framework\TestCase;

/**
 * Quantitative tests of the super parser
 */
class SuperParserTest extends TestCase
{
    public function testSuperParser()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb.xml');
        $data = \cstuder\ParseHydrodaten\SuperParser::parse($raw);

        $this->assertEquals((504 - 3) * 2, $data->getCount());
        $this->assertEquals(5, count($data->getParameters()));
        $this->assertEquals(238, count($data->getLocations()));
        $this->assertEquals(34, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }

    public function testSuperParserWithPreciseData()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb_prec.xml');
        $data = \cstuder\ParseHydrodaten\SuperParser::parse($raw);

        $this->assertEquals(477, $data->getCount());
        $this->assertEquals(5, count($data->getParameters()));
        $this->assertEquals(226 - 6, count($data->getLocations())); // 6 locations do not deliver data
        $this->assertEquals(5, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }

    public function testSuperParserWithNAQUAData()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb.naqua.xml');
        $data = \cstuder\ParseHydrodaten\SuperParser::parse($raw);

        $this->assertEquals(121, $data->getCount());
        $this->assertEquals(6, count($data->getParameters()));
        $this->assertEquals(79 - 20, count($data->getLocations())); // 20 locations do not deliver data
        $this->assertEquals(7, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }

    public function testSuperParserWithLegacyData()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validLegacyData/SMS.xml');
        $data = \cstuder\ParseHydrodaten\SuperParser::parse($raw);

        $this->assertEquals((504 - 3) * 2, $data->getCount());
        $this->assertEquals(5, count($data->getParameters()));
        $this->assertEquals(238, count($data->getLocations()));
        $this->assertEquals(34, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }
}
