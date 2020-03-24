<?php

namespace common\components\validators;

use yii\validators\Validator;

class IsNotArrayValidator extends Validator
{
    public $skipOnEmpty = false;

    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;
        if (is_array($value)) {
            $this->addError($model, $attribute, '{attribute} cant be array.');
        }
    }
}
