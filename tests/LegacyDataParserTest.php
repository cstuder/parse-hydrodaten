<?php

require_once 'DataParserTestCase.php';

use PHPUnit\Framework\DataParserTestCase;

/**
 * Legacy data parser additional tests
 */
class LegacyDataParserTest extends DataParserTestCase
{
    public function testLegacyDataParser()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validLegacyData/SMS.xml');
        $data = \cstuder\ParseHydrodaten\LegacyDataParser::parse($raw);

        $this->assertEquals((504 - 3) * 2, count($data));
        $this->assertEquals(5, count($this->collectParameters($data)));
        $this->assertEquals(238, count($this->collectLocations($data)));
        $this->assertEquals(34, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('StdClass', $data);
    }

    public function testParseWert()
    {
        $this->assertNull(\cstuder\ParseHydrodaten\LegacyDataParser::parseWert(''));
        $this->assertNull(\cstuder\ParseHydrodaten\LegacyDataParser::parseWert(' '));

        $this->assertEquals(0, \cstuder\ParseHydrodaten\LegacyDataParser::parseWert('0'));
        $this->assertEquals(0, \cstuder\ParseHydrodaten\LegacyDataParser::parseWert('0.0'));
        $this->assertEquals(0, \cstuder\ParseHydrodaten\LegacyDataParser::parseWert(' 0 '));

        $this->assertEquals(1, \cstuder\ParseHydrodaten\LegacyDataParser::parseWert('1'));
        $this->assertEquals(2, \cstuder\ParseHydrodaten\LegacyDataParser::parseWert('2.0'));
        $this->assertEquals(3, \cstuder\ParseHydrodaten\LegacyDataParser::parseWert(' 3 '));

        $this->assertEquals(1000, \cstuder\ParseHydrodaten\LegacyDataParser::parseWert('1000'));
        $this->assertEquals(1000, \cstuder\ParseHydrodaten\LegacyDataParser::parseWert('1000.0'));
        $this->assertEquals(1000, \cstuder\ParseHydrodaten\LegacyDataParser::parseWert("1'000.0"));
    }
}
