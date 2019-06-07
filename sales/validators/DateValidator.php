<?php

namespace sales\validators;

use yii\base\Model;

class DateValidator extends \yii\validators\DateValidator
{
    /**
     * @param $model Model
     * @param $attribute
     */
    public function validateAttribute($model, $attribute): void
    {
        parent::validateAttribute($model, $attribute);
        if ($this->format && ($model->$attribute !== date($this->format, strtotime($model->$attribute)))) {
            $model->addError($attribute, 'The format of ' . $model->getAttributeLabel($attribute) . ' is invalid.');
        }
    }
}
