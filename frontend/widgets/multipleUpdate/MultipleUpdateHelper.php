<?php

namespace frontend\widgets\multipleUpdate;

use yii\validators\EachValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;

class MultipleUpdateHelper
{
    public static function validateIds($ids): void
    {
        $requireValidator =  \Yii::createObject(['class' => RequiredValidator::class]);
        if (!$requireValidator->validate($ids, $error)) {
            throw new \DomainException('ids: ' . $error);
        }

        $stringValidator =  \Yii::createObject(['class' => StringValidator::class]);
        if (!$stringValidator->validate($ids, $error)) {
            throw new \DomainException('ids: ' . $error);
        }

        $ids = explode(',', $ids);

        $eachValidator =  \Yii::createObject([
            'class' => EachValidator::class,
            'rule' => ['integer'],
        ]);
        if (!$eachValidator->validate($ids, $error)) {
            throw new \DomainException('ids (every item): ' . $error);
        }
    }
}
