<?php

namespace sales\yii\validators;

use sales\services\client\InternalPhoneException;
use sales\services\client\InternalPhoneGuard;
use yii\validators\Validator;

/**
 * Class InternalPhoneValidate
 *
 * @property InternalPhoneGuard $phoneGuard
 */
class InternalPhoneValidator extends Validator
{
    private $phoneGuard;

    public function __construct(InternalPhoneGuard $phoneGuard, $config = [])
    {
        parent::__construct($config);
        $this->phoneGuard = $phoneGuard;
    }

    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;
        try {
            $this->phoneGuard->guard($value);
        } catch (InternalPhoneException $e) {
            $this->addError($model, $attribute, $e->getMessage());
        }
    }
}
