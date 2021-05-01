<?php

use PHPUnit\Framework\TestCase;

/**
 * Quantitative tests of metadata parsers
 */
class MetadataParserTest extends TestCase
{
    public function testParseMetadata()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb.xml');
        $data = \cstuder\ParseHydrodaten\MetadataParser::parse($raw);

        $this->assertEquals(238, count($data->locations));
        $this->assertEquals(5, count($data->parameters));

        $this->assertContainsOnlyInstancesOf('StdClass', $data->locations);
        $this->assertContainsOnlyInstancesOf('StdClass', $data->parameters);
    }

    public function testParseMetadataPrecise()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb_prec.xml');
        $data = \cstuder\ParseHydrodaten\MetadataParser::parse($raw);

        $this->assertEquals(226, count($data->locations));
        $this->assertEquals(5, count($data->parameters));

        $this->assertContainsOnlyInstancesOf('StdClass', $data->locations);
        $this->assertContainsOnlyInstancesOf('StdClass', $data->parameters);
    }

    public function testParseMetadataNAQUA()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/hydroweb.naqua.xml');
        $data = \cstuder\ParseHydrodaten\MetadataParser::parse($raw);

        $this->assertEquals(79, count($data->locations));
        $this->assertEquals(6, count($data->parameters));

        $this->assertContainsOnlyInstancesOf('StdClass', $data->locations);
        $this->assertContainsOnlyInstancesOf('StdClass', $data->parameters);
    }
}
