<?php

use PHPUnit\Framework\TestCase;

require_once 'DataParserTestCase.php';

/**
 * Test of value parser
 */
class ValueParserTest extends TestCase
{
    public function testParseValue()
    {
        $this->assertNull(\cstuder\ParseHydrodaten\ValueParser::parseValue(''));
        $this->assertNull(\cstuder\ParseHydrodaten\ValueParser::parseValue(null));
        $this->assertNull(\cstuder\ParseHydrodaten\ValueParser::parseValue('NaN'));

        $this->assertEquals(0, \cstuder\ParseHydrodaten\ValueParser::parseValue('0'));
        $this->assertEquals(0, \cstuder\ParseHydrodaten\ValueParser::parseValue('0.0'));
        $this->assertEquals(-5, \cstuder\ParseHydrodaten\ValueParser::parseValue('-5.0'));
        $this->assertEquals(2, \cstuder\ParseHydrodaten\ValueParser::parseValue('2'));
        $this->assertEquals(12345, \cstuder\ParseHydrodaten\ValueParser::parseValue('12345'));
        $this->assertEquals(12345.67, \cstuder\ParseHydrodaten\ValueParser::parseValue('12345.67'));
    }
}
