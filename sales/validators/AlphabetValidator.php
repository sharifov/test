<?php


namespace sales\validators;

use yii\validators\RegularExpressionValidator;

class AlphabetValidator extends RegularExpressionValidator
{
    public $pattern =  '/^[a-zA-Z]+$/';
}