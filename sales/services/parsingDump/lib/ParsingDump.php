<?php

namespace sales\services\parsingDump\lib;

use common\components\SearchService;
use common\models\Quote;

/**
 * Class parsingDump
 */
class ParsingDump
{
    public const PARSING_TYPE_RESERVATION = 'Reservation';
    public const PARSING_TYPE_PRICING = 'Pricing';
    public const PARSING_TYPE_BAGGAGE = 'Baggage';
    public const PARSING_TYPE_ALL = 'ParseAll';
    public const PARSING_DEFAULT_TYPE = self::PARSING_TYPE_ALL;

    public const PARSING_TYPE_MAP = [
        self::PARSING_TYPE_ALL => 'All',
        self::PARSING_TYPE_RESERVATION => 'Reservation',
        self::PARSING_TYPE_PRICING => 'Pricing',
        self::PARSING_TYPE_BAGGAGE => 'Baggage',
    ];

    public const GDS_TYPE_WORLDSPAN = 'worldSpan';
    public const GDS_TYPE_SABRE = 'sabre';
    public const GDS_TYPE_AMADEUS = 'amadeus';

    public const GDS_TYPE_MAP = [
        self::GDS_TYPE_AMADEUS => 'Amadeus',
        self::GDS_TYPE_SABRE => 'Sabre',
        self::GDS_TYPE_WORLDSPAN => 'WorldSpan',
    ];

    public const QUOTE_GDS_TYPE_MAP = [
        SearchService::GDS_AMADEUS => self::GDS_TYPE_AMADEUS,
        SearchService::GDS_SABRE => self::GDS_TYPE_SABRE,
        SearchService::GDS_WORLDSPAN => self::GDS_TYPE_WORLDSPAN,
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
