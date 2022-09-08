<?php

namespace src\helpers;

class NumberHelper
{
    public static function getPercent(float $value, float $maxValue): float
    {
        if ((int) $maxValue === 0) {
            return 0;
        }
        return ($value * 100) / $maxValue;
    }
}
