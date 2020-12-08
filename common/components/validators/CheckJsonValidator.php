<?php

namespace common\components\validators;

use yii\base\Model;
use yii\helpers\Json;
use yii\validators\Validator;

/**
 * Class CheckJsonValidator
 */
class CheckJsonValidator extends Validator
{
    public $skipOnEmpty = false;

    /**
     * @param Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;
        try {
            Json::decode($value);
        } catch (\Throwable $throwable) {
            $this->addError($model, $attribute, '{attribute} is not valid json.');
        }
    }
}
