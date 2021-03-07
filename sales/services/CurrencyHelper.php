<?php

namespace sales\services;

class CurrencyHelper
{
    public static function convertToBaseCurrency($price, $rate)
    {
        if (!$price) {
            throw new \InvalidArgumentException('Invalid price arg.');
        }
        if (!$rate) {
            throw new \InvalidArgumentException('Invalid price rate.');
        }
        //round 2 number
        return self::roundUp(($price / $rate));
    }

    public static function convertFromBaseCurrency($price, $rate)
    {
        if (!$price) {
            throw new \InvalidArgumentException('Invalid price arg.');
        }
        if (!$rate) {
            throw new \InvalidArgumentException('Invalid price rate.');
        }
        return self::roundUp(($price * $rate));
    }

    public static function roundUp(float $price, int $precision = 2)
    {
        return ceil(($price) * (10 ** $precision)) / (10 ** $precision);
    }

    public static function roundDown(float $price, int $precision = 2)
    {
        return floor(($price) * (10 ** $precision)) / (10 ** $precision);
    }
}
