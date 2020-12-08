<?php

namespace common\tests\Gds;

use common\tests\GdsParsing;
use sales\services\parsingDump\lib\ParsingDump;

class GdsSabreParserTest extends GdsParsing
{
    public string $gds = ParsingDump::GDS_TYPE_SABRE;
    public int $expectedSegmentsCount = 5;
    public int $expectedPricesCount = 1;
    public int $expectedBaggageCount = 2;
}
