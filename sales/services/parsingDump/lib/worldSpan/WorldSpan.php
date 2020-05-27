<?php

namespace sales\services\parsingDump\lib\worldSpan;

use sales\services\parsingDump\lib\ParsingDump;

/**
 * Class Gds WorldSpan
 */
class WorldSpan
{
    /**
     * @param string $dump
     * @return string
     */
    public static function getParserType(string $dump): string
    {
        try {
            if (stripos($dump, 'TICKET') !== false) {
                $typeDump = ParsingDump::PARSING_TYPE_PRICING;
            } elseif (stripos($dump, 'BAGGAGE ALLOWANCE') !== false) {
                $typeDump = ParsingDump::PARSING_TYPE_BAGGAGE;
            } elseif (self::findTypeReservation($dump)) {
                $typeDump = ParsingDump::PARSING_TYPE_RESERVATION;
            } else {
                $typeDump = ParsingDump::PARSING_DEFAULT_TYPE;
            }
        } catch (\Throwable $throwable) {
            $typeDump = ParsingDump::PARSING_DEFAULT_TYPE;
        }
        return $typeDump;
    }

    private static function findTypeReservation(string $dump): bool
    {
        preg_match(Reservation::getPatternRow(), $dump, $matches);
        return (!empty($matches));
    }
}