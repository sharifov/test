<?php

namespace common\components\validators;

use yii\base\Model;
use yii\validators\Validator;

class CheckIsBooleanValidator extends Validator
{
    /**
     * @param Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;
        if (!is_bool($value)) {
            $this->addError($model, $attribute, '{attribute} is not boolean');
        }
    }
}
