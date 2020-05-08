<?php

namespace sales\parcingDump\Gds;

/**
 * Class Gds
 */
class Gds
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
            $row = explode("\n", $dump)[0];

            if (stripos($row, 'BAGGAGE ALLOWANCE') !== false) {
                $typeDump = self::TYPE_BAGGAGE;
            } elseif (self::getTypeReservationByFirstString($row)) {
                $typeDump = self::TYPE_RESERVATION;
            } else {
                $typeDump = self::TYPE_PRICING;
            }
        } catch (\Throwable $throwable) {
            $typeDump = self::DEFAULT_TYPE;
        }
        return $typeDump;
    }

    /**
     * @param string $row
     * @return bool
     */
    private static function getTypeReservationByFirstString(string $row): bool
    {
        preg_match("/^(\d{1})\s/s", $row, $matches);
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