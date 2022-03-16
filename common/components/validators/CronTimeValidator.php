<?php

namespace common\components\validators;

use Cron\CronExpression;

class CronTimeValidator
{
    public function isValidTime(string $expression, ?string $dateTime = null): bool
    {
        if (!$dateTime) {
            $dateTime = date('Y-m-d H:i:s');
        }
        $cron = CronExpression::factory($expression);
        return $cron->isDue(new \DateTime($dateTime));
    }
}
