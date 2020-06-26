<?php

namespace common\tests\Gds;

use common\tests\GdsParsing;
use sales\services\parsingDump\lib\ParsingDump;

class GdsAmadeusTest extends GdsParsing
{
    public string $gds = ParsingDump::GDS_TYPE_AMADEUS;
    public int $expectedSegmentsCount = 4;
    public int $expectedPricesCount = 1;
    public int $expectedBaggageCount = 4;
}