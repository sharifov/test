<?php

namespace sales\yii\validators;

use yii\validators\Validator;

class IsArrayValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (!is_array($value)) {
            $this->addError($model, $attribute, ucfirst($attribute) . ' must be array');
        }
    }
}
