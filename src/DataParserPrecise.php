<?php

namespace cstuder\ParseHydrodaten;

/**
 * Parser for Hydrodaten data strings in the `hydroweb2.xsd` format
 * 
 * @link https://www.hydrodaten.admin.ch/lhg/az/xml/hydroweb2.xsd
 */
class DataParserPrecise extends DataParserBase
{
    protected const PARAMETER_ID_ATTRIBUTE = 'name';
}
