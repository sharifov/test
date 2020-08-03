<?php

namespace common\components\validators;

use borales\extensions\phoneInput\PhoneInputValidator;
use sales\helpers\UserCallIdentity;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\validators\DefaultValueValidator;
use yii\validators\FilterValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yii\validators\Validator;

/**
 * Class PhoneValidator
 *
 * @property bool $required
 * @property bool $allowClientSellerNumbers
 * @property int $stringMax
 * @property bool $boralesValidatorEnable
 *
 * Ex:
 *
    public function rules(): array
    {
        return [
            ['phone', \common\components\validators\PhoneValidator::class, 'required' => true],
        ];
    }
 *
 * Equivalent:
 *
    public function rules(): array
    {
        return [
            ['phone', 'required'],
            ['phone', 'default', 'value' => null],
            ['phone', 'filter', 'filter' => static function($value) {
                return $value === null ? null : str_replace(['-', ' '], '', trim($value));
            }],
            ['phone', 'string', 'max' => 15],
            ['phone', borales\extensions\phoneInput\PhoneInputValidator::class],
        ];
    }
 */
class PhoneValidator extends Validator
{
    public $skipOnEmpty = false;
    public $required = false;
    public $allowClientSellerNumbers = false;
    public $stringMax = 15;
    public $boralesValidatorEnable = true;

    /**
     * @param Model $model
     * @param string $attribute
     * @throws InvalidConfigException
     */
    public function validateAttribute($model, $attribute): void
    {
        $default = Yii::createObject([
            'class' => DefaultValueValidator::class,
            'value' => null,
        ]);
        $default->validateAttribute($model, $attribute);

        if ($this->required) {
            $require = Yii::createObject(['class' => RequiredValidator::class]);
            $require->validateAttribute($model, $attribute);
            if ($model->hasErrors()) {
                return;
            }
        }

        if ($model->{$attribute} === null) {
            return;
        }

        $filter = Yii::createObject([
            'class' => FilterValidator::class,
            'filter' => static function ($value) {
                return $value === null ? null : str_replace(['-', ' ', '(', ')'], '', trim($value));
            }
        ]);
        $filter->validateAttribute($model, $attribute);

        if ($this->allowClientSellerNumbers) {
            $clientPrefix = UserCallIdentity::getClientPrefix();
            preg_match('/^' . $clientPrefix . '/', $model->{$attribute}, $matches);
            if ($matches) {
                return;
            }
        }

        $string = Yii::createObject([
            'class' => StringValidator::class,
            'max' => (int)$this->stringMax,
        ]);
        $string->validateAttribute($model, $attribute);
        if ($model->hasErrors()) {
            return;
        }

        if ($this->boralesValidatorEnable) {
            $phoneInput = Yii::createObject([
                'class' => PhoneInputValidator::class,
            ]);
            $phoneInput->validateAttribute($model, $attribute);
            if ($model->hasErrors()) {
                return;
            }
        }
    }
}
