<?php

namespace sales\services\client;

use yii\validators\Validator;

/**
 * Class InternalPhoneValidator
 *
 * @property InternalPhoneGuard $phoneGuard
 * @property bool $allowInternalPhone;
 */
class InternalPhoneValidator extends Validator
{
    public $allowInternalPhone = false;

    private $phoneGuard;

    public function __construct(InternalPhoneGuard $phoneGuard, $config = [])
    {
        parent::__construct($config);
        $this->phoneGuard = $phoneGuard;
    }

    public function validateAttribute($model, $attribute): void
    {
        if ($this->allowInternalPhone === false) {
            $value = $model->$attribute;
            try {
                $this->phoneGuard->guard($value);
            } catch (InternalPhoneException $e) {
                $this->addError($model, $attribute, $e->getMessage());
            }
        }
    }
}
