<?php
require_once 'DataParserTestCase.php';

use PHPUnit\Framework\DataParserTestCase;

/**
 * Quantitative tests of data parsers
 */
class DataParserPreciseTest extends DataParserTestCase
{
    public function testDataParserPrecise()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb_prec.xml');
        $data = \cstuder\ParseHydrodaten\DataParserPrecise::parse($raw);

        $this->assertEquals(477, count($data->values));
        $this->assertEquals(5, count($this->collectParameters($data)));
        $this->assertEquals(226 - 6, count($this->collectLocations($data))); // 6 locations do not deliver data
        $this->assertEquals(5, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->values);
    }

    public function testDataParserPreciseNAQUA()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb.naqua.xml');
        $data = \cstuder\ParseHydrodaten\DataParserPrecise::parse($raw);

        $this->assertEquals(121, count($data->values));
        $this->assertEquals(6, count($this->collectParameters($data)));
        $this->assertEquals(79 - 20, count($this->collectLocations($data))); // 20 locations do not deliver data
        $this->assertEquals(7, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->values);
    }
}
