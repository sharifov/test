<?php

namespace sales\validators;

use yii\validators\Validator;

/**
 * Class SlugValidator
 */
class SlugValidator extends Validator
{
    public function validateAttribute($model, $attribute): void
    {
        $checkResult = preg_match('/^[a-z0-9_]+$/', $model->$attribute);
        if (!$checkResult) {
            $this->addError($model, $attribute, 'Value should only contain numbers, small letters and underscores');
        }
    }
}
