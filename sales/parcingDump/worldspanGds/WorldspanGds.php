<?php

namespace sales\parcingDump\worldspanGds;

/**
 * Class WorldspanGds
 */
class WorldspanGds
{
    public CONST TYPE_MAP = [
        'Reservation' => 'Reservation',
        'PricingPublished' => 'Pricing published',
        'PricingPrivate' => 'Pricing private',
        'Baggage' => 'Baggage',
    ];

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