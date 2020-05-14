<?php

namespace sales\services\parsingDump\Sabre;

/**
 * Class Gds
 */
class Sabre
{
    public CONST TYPE_RESERVATION = 'Reservation';
    public CONST TYPE_PRICING = 'Pricing';
    public CONST TYPE_BAGGAGE = 'Baggage';
    public CONST TYPE_ALL = 'All';

    public CONST TYPE_MAP = [
        self::TYPE_ALL => 'All',
        self::TYPE_RESERVATION => 'Reservation',
        self::TYPE_PRICING => 'Pricing',
        self::TYPE_BAGGAGE => 'Baggage',
    ];

    public CONST DEFAULT_TYPE = self::TYPE_ALL;

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
            } elseif (self::findTypeReservation($dump)) {
                $typeDump = self::TYPE_RESERVATION;
            } else {
                $typeDump = self::DEFAULT_TYPE;
            }
        } catch (\Throwable $throwable) {
            $typeDump = self::DEFAULT_TYPE;
        }
        return $typeDump;
    }

    private static function findTypeReservation(string $dump): bool
    {
        preg_match(Reservation::getPatternRow(), $dump, $matches);
        return (!empty($matches));
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