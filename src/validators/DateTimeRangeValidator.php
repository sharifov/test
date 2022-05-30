<?php

namespace src\validators;

use src\auth\Auth;
use src\helpers\DateHelper;
use yii\validators\Validator;

class DateTimeRangeValidator extends Validator
{
    public string $separator = ' - ';

    public function validateAttribute($model, $attribute): void
    {
        $dates = explode($this->separator, $model->{$attribute}, 2);
        if (count($dates) !== 2) {
            $this->addError($model, $attribute, 'Date Time Range incorrect format');
            return;
        }
        if (!DateHelper::checkDateTime($dates[0], 'Y-m-d H:i')) {
            $this->addError($model, $attribute, 'Start DateTime incorrect format');
            return;
        }

        $startDateTime = new \DateTimeImmutable($dates[0], ($timezone = Auth::user()->timezone) ? new \DateTimeZone($timezone) : null);
        $nowDateTime = new \DateTimeImmutable('now', ($timezone = Auth::user()->timezone) ? new \DateTimeZone($timezone) : null);
        if ($startDateTime < $nowDateTime) {
            $this->addError($model, $attribute, 'Start DateTime must be more than now');
        }
    }
}
