<?php

namespace sales\services\parsingDump\worldSpan;

/**
 * Class Gds
 */
class WorldSpan
{
    public CONST TYPE_RESERVATION = 'Reservation';
    public CONST TYPE_PRICING = 'Pricing';
    public CONST TYPE_BAGGAGE = 'Baggage';

    public CONST TYPE_MAP = [
        self::TYPE_RESERVATION => 'Reservation',
        self::TYPE_PRICING => 'Pricing',
        self::TYPE_BAGGAGE => 'Baggage',
    ];

    public CONST DEFAULT_TYPE = self::TYPE_RESERVATION;

    /**
     * @param string $dump
     * @return string
     */
    public static function getParserType(string $dump): string
    {
        try {
            if (stripos($dump, 'TICKET') !== false) {
                $typeDump = self::TYPE_PRICING;
            } elseif (stripos($dump, 'BAGGAGE ALLOWANCE') !== false) {
                $typeDump = self::TYPE_BAGGAGE;
            } else {
                 $typeDump = self::TYPE_RESERVATION;
            }
        } catch (\Throwable $throwable) {
            $typeDump = self::DEFAULT_TYPE;
        }
        return $typeDump;
    }

    /**
     * @param string $class
     * @return mixed|Reservation
     */
    public static function initClass(string $class)
    {
        $nameClass = __NAMESPACE__ . '\\' . $class;

        if (array_key_exists($class, self::TYPE_MAP) && class_exists($nameClass)) {
            return new $nameClass();
        }
        return new Reservation();
    }


}