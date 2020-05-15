<?php

namespace sales\services\parsingDump\lib;

/**
 * Class parsingDump
 */
class ParsingDump
{
    public CONST PARSING_TYPE_RESERVATION = 'Reservation';
    public CONST PARSING_TYPE_PRICING = 'Pricing';
    public CONST PARSING_TYPE_BAGGAGE = 'Baggage';
    public CONST PARSING_TYPE_ALL = 'All';
    public CONST DEFAULT_TYPE = self::PARSING_TYPE_ALL;

    public CONST PARSING_TYPE_MAP = [
        self::PARSING_TYPE_ALL => 'All',
        self::PARSING_TYPE_RESERVATION => 'Reservation',
        self::PARSING_TYPE_PRICING => 'Pricing',
        self::PARSING_TYPE_BAGGAGE => 'Baggage',
    ];

    public CONST GDS_TYPE_WORLDSPAN = 'worldSpan';
    public CONST GDS_TYPE_SABRE = 'sabre';

    public CONST GDS_TYPE_MAP = [
        self::GDS_TYPE_WORLDSPAN => 'WorldSpan',
        self::GDS_TYPE_SABRE => 'Sabre',
    ];

    public static function initClass(string $class)
    {
        $nameClass = __NAMESPACE__ . '\\' . $class;

        if (array_key_exists($class, self::TYPE_MAP) && class_exists($nameClass)) {
            return new $nameClass();
        }
        return new Reservation();
    }
}