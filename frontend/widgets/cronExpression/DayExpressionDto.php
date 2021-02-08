<?php

namespace frontend\widgets\cronExpression;

class DayExpressionDto
{
    public const EXPRESSION_EVERY_DAY = 1;
    public const EXPRESSION_EVEN_DAYS = 2;
    public const EXPRESSION_ODD_DAYS = 3;
    public const EXPRESSION_EVERY_FIVE_DAYS = 4;
    public const EXPRESSION_EVERY_THEN_DAYS = 5;
    public const EXPRESSION_EVERY_HALF_MONTH = 6;

    public const EXPRESSION_LIST = [
        self::EXPRESSION_EVERY_DAY => 'Every Day',
        self::EXPRESSION_EVEN_DAYS => 'Even Days',
        self::EXPRESSION_ODD_DAYS => 'Odd Days',
        self::EXPRESSION_EVERY_FIVE_DAYS => 'Every 5 Days',
        self::EXPRESSION_EVERY_THEN_DAYS => 'Every 10 Days',
        self::EXPRESSION_EVERY_HALF_MONTH => 'Every Half Month',
    ];

    public const EXPRESSION_FORMAT = [
        self::EXPRESSION_EVERY_DAY => '*',
        self::EXPRESSION_EVEN_DAYS => '*/2',
        self::EXPRESSION_ODD_DAYS => '1-31/2',
        self::EXPRESSION_EVERY_FIVE_DAYS => '*/5',
        self::EXPRESSION_EVERY_THEN_DAYS => '*/10',
        self::EXPRESSION_EVERY_HALF_MONTH => '*/15',
    ];
}
