<?php

namespace src\services;

use common\models\Currency;

class CurrencyHelper
{
    public static function convertToBaseCurrency($price, $rate)
    {
//        if (!$price) {
//            throw new \InvalidArgumentException('Invalid price arg.');
//        }
        if (!$rate) {
            throw new \InvalidArgumentException('Invalid price rate.');
        }
        //round 2 number
        return self::roundUp(($price / $rate));
    }

    public static function convertFromBaseCurrency($price, $rate)
    {
//        if (!$price) {
//            throw new \InvalidArgumentException('Invalid price arg.');
//        }
//        if (!$rate) {
//            throw new \InvalidArgumentException('Invalid price rate.');
//        }
        return self::roundUp(($price * $rate));
    }

    public static function roundUp(float $price, int $precision = 2)
    {

        if ((strlen($price) - strrpos($price, '.') - 1) <= $precision) {
            return $price;
        }

        return ceil(($price) * (10 ** $precision)) / (10 ** $precision);
    }

    public static function roundDown(float $price, int $precision = 2)
    {
        if ((strlen($price) - strrpos($price, '.') - 1) <= $precision) {
            return $price;
        }

        return floor(($price) * (10 ** $precision)) / (10 ** $precision);
    }

    public static function getSymbolByCode(?string $code, int $cacheDuration = 60): string
    {
        if (empty($code)) {
            return '';
        }
        if (!$currency = Currency::find()->byCode($code)->addCache($cacheDuration)->limit(1)->one()) {
            return $code;
        }
        if (empty($currency->cur_symbol)) {
            return $code;
        }
        return $currency->cur_symbol;
    }

    public static function getAppRateByCode(string $code): ?float
    {
        if (!$currency = Currency::find()->byCode($code)->limit(1)->one()) {
            return null;
        }
        return $currency->cur_app_rate;
    }
}
