<?php

namespace common\components\validators;

use frontend\helpers\JsonHelper;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Class CheckAndConvertToJsonValidator
 */
class CheckAndConvertToJsonValidator extends Validator
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
            $model->$attribute = JsonHelper::decode($value);
        } catch (\Throwable $throwable) {
            $this->addError($model, $attribute, '{attribute} is not valid json.');
        }
    }
}
