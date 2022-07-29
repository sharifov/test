<?php

namespace src\access;

use Cron\CronExpression;

class CronExpressionService
{
    public static function isDueCronExpression(string $expression, $currentTime = 'now'): bool
    {
        return CronExpression::factory($expression)->isDue($currentTime);
    }
}
