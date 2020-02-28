<?php

namespace sales\yii\validators;

class NumberValidator extends \yii\validators\NumberValidator
{
    public const PRICE_PATTERN = '/^([1-9][0-9]*|0)(\.[0-9]{2})?$/';
}
