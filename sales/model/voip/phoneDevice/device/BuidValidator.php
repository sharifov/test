<?php

namespace sales\model\voip\phoneDevice\device;

use common\components\validators\AlphabetValidator;
use yii\validators\StringValidator;
use yii\validators\Validator;

class BuidValidator extends Validator
{
    protected function validateValue($value)
    {
        $stringValidator = new StringValidator([
            'min' => 10,
            'max' => 10,
            'skipOnEmpty' => false,
        ]);
        if (!$stringValidator->validate($value, $error)) {
            unset($stringValidator);
            return [ucfirst($error), []];
        }
        unset($stringValidator);

        $alphabetValidator = new AlphabetValidator([
            'skipOnEmpty' => false
        ]);
        if (!$alphabetValidator->validate($value, $error)) {
            unset($alphabetValidator);
            return [ucfirst($error), []];
        }
        unset($alphabetValidator);

        return null;
    }
}
