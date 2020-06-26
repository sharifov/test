<?php

namespace common\tests\Gds;

use common\tests\GdsParsing;
use sales\services\parsingDump\lib\ParsingDump;

class GdsSabreTest extends GdsParsing
{
    public string $gds = ParsingDump::GDS_TYPE_SABRE;
    public int $expectedSegmentsCount = 4; /* TODO::  */
    public int $expectedPricesCount = 1; /* TODO::  */
    public int $expectedBaggageCount; /* TODO::  */
}