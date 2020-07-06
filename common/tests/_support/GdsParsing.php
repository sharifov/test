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
        $message = 'Reservation test failed';
        try {
            $dump = include(codecept_data_dir() . 'dump.' . $this->gds . '.php');
            $parserObj = ParsingDump::initClass($this->gds, ParsingDump::PARSING_TYPE_RESERVATION);
            $parsed = $parserObj->parseDump($dump);
        } catch (\Throwable $throwable) {
            $parsed = [];
            $message = $throwable->getMessage();
        }
        self::assertArrayHasKey('reservation', $parsed, $message);
        self::assertCount($this->expectedSegmentsCount, $parsed['reservation']);
    }

    public function testParseBaggage(): void
    {
        $message = 'Baggage test failed';
        try {
            $dump = include(codecept_data_dir() . 'dump.' . $this->gds . '.php');
            $parserObj = ParsingDump::initClass($this->gds, ParsingDump::PARSING_TYPE_BAGGAGE);
            $parsed = $parserObj->parseDump($dump);
        } catch (\Throwable $throwable) {
            $parsed = [];
            $message = $throwable->getMessage();
        }
        self::assertArrayHasKey('baggage', $parsed, $message);
        self::assertCount($this->expectedBaggageCount, $parsed['baggage']);
    }

    public function testParsePricing(): void
    {
        $message = 'Pricing test failed';
        try {
            $dump = include(codecept_data_dir() . 'dump.' . $this->gds . '.php');
            $parserObj = ParsingDump::initClass($this->gds, ParsingDump::PARSING_TYPE_PRICING);
            $parsed = $parserObj->parseDump($dump);
        } catch (\Throwable $throwable) {
            $parsed = [];
            $message = $throwable->getMessage();
        }
        self::assertArrayHasKey('prices', $parsed, $message);
        self::assertCount($this->expectedPricesCount, $parsed['prices']);
    }
}