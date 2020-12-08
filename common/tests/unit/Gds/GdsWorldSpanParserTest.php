<?php

namespace common\tests\Gds;

use common\tests\GdsParsing;
use sales\services\parsingDump\lib\ParsingDump;

class GdsWorldSpanParserTest extends GdsParsing
{
    public string $gds = ParsingDump::GDS_TYPE_WORLDSPAN;
    public int $expectedSegmentsCount = 3;
    public int $expectedPricesCount = 1;
    public int $expectedBaggageCount = 4;
}
