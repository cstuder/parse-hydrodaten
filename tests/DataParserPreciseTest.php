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

        $this->assertEquals((504 - 3) * 2, count($data));
        $this->assertEquals(5, count($this->collectParameters($data)));
        $this->assertEquals(238, count($this->collectLocations($data)));
        $this->assertEquals(34, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('StdClass', $data);
    }

    public function testDataParserPreciseNAQUA()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb.naqua.xml');
        $data = \cstuder\ParseHydrodaten\DataParserPrecise::parse($raw);

        $this->assertEquals((504 - 3) * 2, count($data));
        $this->assertEquals(5, count($this->collectParameters($data)));
        $this->assertEquals(238, count($this->collectLocations($data)));
        $this->assertEquals(34, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('StdClass', $data);
    }
}
