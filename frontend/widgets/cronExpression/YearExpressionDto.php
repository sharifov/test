<?php

namespace frontend\widgets\cronExpression;

class YearExpressionDto
{
    public const MAX_YEAR = 2040;

    public const EXPRESSION_EVERY_YEAR = 1;

    public const EXPRESSION_LIST = [
        self::EXPRESSION_EVERY_YEAR => 'Every Year',
    ];

    public const EXPRESSION_FORMAT = [
        self::EXPRESSION_EVERY_YEAR => '*',
    ];

    public static function getMinYear(): string
    {
        $date = new \DateTime();
        return $date->format('Y');
    }

    public static function getYearsRange(): array
    {
        $range = [];
        for ($min = (int)self::getMinYear(); $min <= self::MAX_YEAR; $min++) {
            $range[] = $min;
        }
        return $range;
    }
}
