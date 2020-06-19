<?php

namespace sales\services\parsingDump\lib;

use common\models\Quote;

/**
 * Class parsingDump
 */
class ParsingDump
{
    public CONST PARSING_TYPE_RESERVATION = 'Reservation';
    public CONST PARSING_TYPE_PRICING = 'Pricing';
    public CONST PARSING_TYPE_BAGGAGE = 'Baggage';
    public CONST PARSING_TYPE_ALL = 'ParseAll';
    public CONST PARSING_DEFAULT_TYPE = self::PARSING_TYPE_ALL;

    public CONST PARSING_TYPE_MAP = [
        self::PARSING_TYPE_ALL => 'All',
        self::PARSING_TYPE_RESERVATION => 'Reservation',
        self::PARSING_TYPE_PRICING => 'Pricing',
        self::PARSING_TYPE_BAGGAGE => 'Baggage',
    ];

    public CONST GDS_TYPE_WORLDSPAN = 'worldSpan';
    public CONST GDS_TYPE_SABRE = 'sabre';
    public CONST GDS_TYPE_AMADEUS = 'amadeus';

    public CONST GDS_TYPE_MAP = [
        self::GDS_TYPE_AMADEUS => 'Amadeus',
        self::GDS_TYPE_SABRE => 'Sabre',
        self::GDS_TYPE_WORLDSPAN => 'WorldSpan',
    ];

    public CONST QUOTE_GDS_TYPE_MAP = [
        //Quote::GDS_AMADEUS => self::GDS_TYPE_AMADEUS, /* TODO::  */
        Quote::GDS_SABRE => self::GDS_TYPE_SABRE,
        Quote::GDS_WORLDSPAN => self::GDS_TYPE_WORLDSPAN,
    ];

    /**
     * @param string $gds
     * @param string $class
     * @return mixed|null
     */
    public static function initClass(string $gds, string $class)
    {
        $nameClass = __NAMESPACE__ . '\\' . $gds . '\\' . $class;

        if (class_exists($nameClass)) {
            return new $nameClass();
        }
        return null;
    }

    /**
     * @param string $gdsQuote
     * @return null|string
     */
    public static function getGdsByQuote(string $gdsQuote): ?string
    {
        if (array_key_exists($gdsQuote, self::QUOTE_GDS_TYPE_MAP)) {
            return self::QUOTE_GDS_TYPE_MAP[$gdsQuote];
        }
        return null;
    }

    /**
     * @param string $gds
     * @return string
     */
    public static function setGdsForParsing(string $gds): string
    {
        if (array_key_exists($gds, self::GDS_TYPE_MAP)) {
            return $gds;
        }
        if ($dumpGds = self::getGdsByQuote($gds)) {
            return $dumpGds;
        }
        throw new \DomainException('This GDS (' . $gds . ') cannot be processed');
    }
}