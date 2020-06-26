<?php

namespace common\tests;

use Codeception\Test\Unit;
use sales\services\parsingDump\lib\ParsingDump;

/**
 * Class AbstractGdsParsing
 */
class GdsParsing extends Unit
{
    public string $gds;
    public int $expectedSegmentsCount;
    public int $expectedPricesCount;
    public int $expectedBaggageCount;

    public function testParseReservation(): void
    {
        try {
            $dump = include(codecept_data_dir() . 'dump.' . $this->gds . '.php');
            $parserObj = ParsingDump::initClass($this->gds, ParsingDump::PARSING_TYPE_RESERVATION);
            $parsed = $parserObj->parseDump($dump);
        } catch (\Throwable $throwable) {
            $parsed = [];
        }
        self::assertArrayHasKey('reservation', $parsed);
        self::assertCount($this->expectedSegmentsCount, $parsed['reservation']);
    }

    public function testParseBaggage(): void
    {
        try {
            $dump = include(codecept_data_dir() . 'dump.' . $this->gds . '.php');
            $parserObj = ParsingDump::initClass($this->gds, ParsingDump::PARSING_TYPE_BAGGAGE);
            $parsed = $parserObj->parseDump($dump);
        } catch (\Throwable $throwable) {
            $parsed = [];
        }
        self::assertArrayHasKey('baggage', $parsed);
        self::assertCount($this->expectedSegmentsCount, $parsed['baggage']);
    }

    public function testParsePricing(): void
    {
        try {
            $dump = include(codecept_data_dir() . 'dump.' . $this->gds . '.php');
            $parserObj = ParsingDump::initClass($this->gds, ParsingDump::PARSING_TYPE_PRICING);
            $parsed = $parserObj->parseDump($dump);
        } catch (\Throwable $throwable) {
            $parsed = [];
        }
        self::assertArrayHasKey('prices', $parsed);
        self::assertCount($this->expectedPricesCount, $parsed['prices']);
    }
}