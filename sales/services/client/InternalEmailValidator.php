<?php

namespace sales\services\client;

use yii\base\Model;
use yii\validators\Validator;

/**
 * Class InternalEmailValidator
 *
 * @property InternalEmailGuard $emailGuard
 * @property bool $allowInternalEmail
 */
class InternalEmailValidator extends Validator
{
    public $allowInternalEmail = false;

    private $emailGuard;

    /**
     * InternalEmailValidator constructor.
     * @param InternalEmailGuard $emailGuard
     * @param array $config
     */
    public function __construct(InternalEmailGuard $emailGuard, $config = [])
    {
        parent::__construct($config);
        $this->emailGuard = $emailGuard;
    }

    /**
     * @param Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute): void
    {
        if ($this->allowInternalEmail === false) {
            $value = $model->$attribute;
            try {
                $this->emailGuard->guard($value);
            } catch (InternalEmailException $e) {
                $this->addError($model, $attribute, $e->getMessage());
            }
        }
    }
}
