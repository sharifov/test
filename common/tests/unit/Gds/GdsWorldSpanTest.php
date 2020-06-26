<?php

namespace common\tests\Gds;

use common\tests\GdsParsing;
use sales\services\parsingDump\lib\ParsingDump;

class GdsWorldSpanTest extends GdsParsing
{
    public string $gds = ParsingDump::GDS_TYPE_WORLDSPAN;
    public int $expectedSegmentsCount = 4; /* TODO::  */
    public int $expectedPricesCount = 1; /* TODO::  */
    public int $expectedBaggageCount; /* TODO::  */
}