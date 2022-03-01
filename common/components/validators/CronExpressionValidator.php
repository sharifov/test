<?php

namespace common\components\validators;

use yii\validators\Validator;

class CronExpressionValidator extends Validator
{
    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;
        $valid = \Cron\CronExpression::isValidExpression($value);
        if (!$valid) {
            $this->addError($model, $attribute, 'Invalid CRON Expression');
        }
    }
}
