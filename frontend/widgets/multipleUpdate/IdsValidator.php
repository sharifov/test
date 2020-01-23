<?php

namespace frontend\widgets\multipleUpdate;

use yii\validators\EachValidator;
use yii\validators\FilterValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yii\validators\Validator;

class IdsValidator extends Validator
{
    public function validateAttribute($model, $attribute): void
    {
        $require = \Yii::createObject(['class' => RequiredValidator::class]);
        $require->validateAttribute($model, $attribute);
        if ($model->hasErrors()) {
            return;
        }

        $require = \Yii::createObject(['class' => StringValidator::class]);
        $require->validateAttribute($model, $attribute);
        if ($model->hasErrors()) {
            return;
        }

        $filter = \Yii::createObject([
            'class' => FilterValidator::class,
            'filter' => static function ($value) {
                return explode(',', $value);
            }
        ]);
        $filter->validateAttribute($model, $attribute);

        $eachValidator = \Yii::createObject([
            'class' => EachValidator::class,
            'rule' => ['integer']
        ]);
        $eachValidator->validateAttribute($model, $attribute);
        if ($model->hasErrors()) {
            return;
        }

        $filter = \Yii::createObject([
            'class' => FilterValidator::class,
            'filter' => static function($value) {
                $new = [];
                foreach ($value as $item) {
                    $new[] = (int)$item;
                }
                return $new;
            }
        ]);
        $filter->validateAttribute($model, $attribute);
    }
}
