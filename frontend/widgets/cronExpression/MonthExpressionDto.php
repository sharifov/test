<?php

namespace frontend\widgets\cronExpression;

class MonthExpressionDto
{
    public const EXPRESSION_EVERY_MONTH = 1;
    public const EXPRESSION_EVEN_MONTHS = 2;
    public const EXPRESSION_ODD_MONTHS = 3;
    public const EXPRESSION_EVERY_FOUR_MONTHS = 4;
    public const EXPRESSION_EVERY_HALF_YEAR = 5;

    public const EXPRESSION_LIST = [
        self::EXPRESSION_EVERY_MONTH => 'Every Month',
        self::EXPRESSION_EVEN_MONTHS => 'Even Months',
        self::EXPRESSION_ODD_MONTHS => 'Odd Months',
        self::EXPRESSION_EVERY_FOUR_MONTHS => 'Every 4 months',
        self::EXPRESSION_EVERY_HALF_YEAR => 'Every Half Year',
    ];

    public const EXPRESSION_FORMAT = [
        self::EXPRESSION_EVERY_MONTH => '*',
        self::EXPRESSION_EVEN_MONTHS => '*/2',
        self::EXPRESSION_ODD_MONTHS => '1-11/2',
        self::EXPRESSION_EVERY_FOUR_MONTHS => '*/4',
        self::EXPRESSION_EVERY_HALF_YEAR => '*/6',
    ];
}
