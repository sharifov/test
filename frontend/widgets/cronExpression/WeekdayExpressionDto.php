<?php

namespace frontend\widgets\cronExpression;

class WeekdayExpressionDto
{
    public const EXPRESSION_EVERY_WEEKDAY = 1;
    public const EXPRESSION_MONDAY_FRIDAY = 2;
    public const EXPRESSION_WEEKEND_DAYS = 3;

    public const EXPRESSION_LIST = [
        self::EXPRESSION_EVERY_WEEKDAY => 'Every Weekday',
        self::EXPRESSION_MONDAY_FRIDAY => 'Monday-Friday',
        self::EXPRESSION_WEEKEND_DAYS => 'Weekend Days',
    ];

    public const EXPRESSION_FORMAT = [
        self::EXPRESSION_EVERY_WEEKDAY => '*',
        self::EXPRESSION_MONDAY_FRIDAY => '1-5',
        self::EXPRESSION_WEEKEND_DAYS => '0,6',
    ];
}
