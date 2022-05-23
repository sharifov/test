<?php

namespace common\components\validators;

use yii\validators\Validator;

class KeyValidator extends Validator
{
    public function validateAttribute($model, $attribute): void
    {
        $checkResult = preg_match('/^[a-z0-9_]+$/', $model->$attribute);
        if (!$checkResult) {
            $this->addError($model, $attribute, 'Key should only contain numbers, small letters and underscores');
        }
    }
}
