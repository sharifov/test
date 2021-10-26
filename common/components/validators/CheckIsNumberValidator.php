<?php

namespace common\components\validators;

use yii\base\Model;

class CheckIsNumberValidator extends \yii\validators\Validator
{
    public bool $allowInt = false;

    /**
     * @param Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;
        if (!is_float($value) && !($this->allowInt && is_int($value))) {
            $this->addError($model, $attribute, '{attribute} is not number');
        }
    }
}
