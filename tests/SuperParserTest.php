<?php
require_once 'DataParserTestCase.php';

use PHPUnit\Framework\DataParserTestCase;

/**
 * Quantitative tests of the super parser
 */
class SuperParserTest extends DataParserTestCase
{
    public function testSuperParser()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb.xml');
        $data = \cstuder\ParseHydrodaten\SuperParser::parse($raw);

        $this->assertEquals((504 - 3) * 2, count($data));
        $this->assertEquals(5, count($this->collectParameters($data)));
        $this->assertEquals(238, count($this->collectLocations($data)));
        $this->assertEquals(34, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('StdClass', $data);
    }

    public function testSuperParserWithPreciseData()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb_prec.xml');
        $data = \cstuder\ParseHydrodaten\SuperParser::parse($raw);

        $this->assertEquals(477, count($data));
        $this->assertEquals(5, count($this->collectParameters($data)));
        $this->assertEquals(226 - 6, count($this->collectLocations($data))); // 6 locations do not deliver data
        $this->assertEquals(5, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('StdClass', $data);
    }

    public function testSuperParserWithNAQUAData()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb.naqua.xml');
        $data = \cstuder\ParseHydrodaten\SuperParser::parse($raw);

        $this->assertEquals(121, count($data));
        $this->assertEquals(6, count($this->collectParameters($data)));
        $this->assertEquals(79 - 20, count($this->collectLocations($data))); // 20 locations do not deliver data
        $this->assertEquals(7, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('StdClass', $data);
    }

    public function testSuperParserWithLegacyData()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validLegacyData/SMS.xml');
        $data = \cstuder\ParseHydrodaten\SuperParser::parse($raw);

        $this->assertEquals((504 - 3) * 2, count($data));
        $this->assertEquals(5, count($this->collectParameters($data)));
        $this->assertEquals(238, count($this->collectLocations($data)));
        $this->assertEquals(34, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('StdClass', $data);
    }
}
