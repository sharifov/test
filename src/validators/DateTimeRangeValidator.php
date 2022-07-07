<?php

namespace src\validators;

use modules\shiftSchedule\src\abac\ShiftAbacObject;
use src\auth\Auth;
use src\helpers\DateHelper;
use Yii;
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

        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE_PAST_EVENT, Access to create past event */
        if ($startDateTime < $nowDateTime && !Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE_PAST_EVENT)) {
            $this->addError($model, $attribute, 'Start DateTime must be more than now');
        }
    }
}
