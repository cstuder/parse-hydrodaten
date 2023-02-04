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

        $this->assertEquals((504 - 3) * 2, $data->getCount());
        $this->assertEquals(5, count($data->getParameters()));
        $this->assertEquals(238, count($data->getLocations()));
        $this->assertEquals(34, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }
}
