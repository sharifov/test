<?php

namespace common\components\validators;

use yii\validators\Validator;

class IsArrayValidator extends Validator
{
    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;
        if (!is_array($value)) {
            $this->addError($model, $attribute, ucfirst($attribute) . ' must be array');
        }
    }
}
