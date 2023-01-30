<?php
require_once 'DataParserTestCase.php';

use PHPUnit\Framework\DataParserTestCase;

/**
 * Quantitative tests of data parsers
 */
class DataParserTest extends DataParserTestCase
{
    public function testDataParser()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb.xml');
        $data = \cstuder\ParseHydrodaten\DataParser::parse($raw);

        $this->assertEquals((504 - 3) * 2, count($data->values));
        $this->assertEquals(5, count($this->collectParameters($data)));
        $this->assertEquals(238, count($this->collectLocations($data)));
        $this->assertEquals(34, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->values);
    }
}
