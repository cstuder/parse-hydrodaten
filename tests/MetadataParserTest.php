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
}
