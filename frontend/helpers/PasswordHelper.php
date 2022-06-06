<?php

namespace frontend\helpers;

use kartik\password\StrengthValidator;
use yii\base\Model;
use yii\bootstrap4\Html;

class PasswordHelper
{
    public static function getRulesList($validators): array
    {
        $strengthValidator = null;

        foreach ($validators as $validator) {
            if ($validator instanceof StrengthValidator) {
                $strengthValidator = $validator;
                break;
            }
        }

        if ($strengthValidator === null) {
            return [];
        }

        $rules = [];

        if ($strengthValidator->hasUser === true) {
            $rules[] = "Password cannot contain the username";
        }

        if ($strengthValidator->hasEmail === true) {
            $rules[] = "Password cannot contain an email address";
        }

        if ($strengthValidator->min > 0) {
            $rules[] = "Minimum password length is {$strengthValidator->min} characters";
        }

        if ($strengthValidator->lower > 0) {
            $rules[] = "Password must contain at least {$strengthValidator->lower} lower case characters";
        }

        if ($strengthValidator->upper > 0) {
            $rules[] = "Password must contain at least {$strengthValidator->upper} upper case characters";
        }

        if ($strengthValidator->digit > 0) {
            $rules[] = "Password must contain at least {$strengthValidator->digit} numeric/digit characters";
        }

        if ($strengthValidator->special > 0) {
            $rules[] = "Password must contain at least {$strengthValidator->special} special characters";
        }

        if ($strengthValidator->allowSpaces === false) {
            $rules[] = "Password cannot contain any spaces";
        }

        return $rules;
    }

    public static function getLabelWithTooltip(Model $model, string $attribute): string
    {
        $rules = self::getRulesList($model->getActiveValidators($attribute));
        $label = $model->getAttributeLabel($attribute);

        if (empty($rules)) {
            return $label;
        }

        $template = '<div class="alert alert-info p-2"><h4 class="text-center">Strength Validation Rules</h4><ul class="m-0 pl-3">';

        foreach ($rules as $rule) {
            $template .= "<li>{$rule}</li>";
        }

        $template .= '</ul></div>';

        return $label . ' ' . Html::tag('i', '', [
            'class' => 'fa fa-info-circle info',
            'data-toggle' => 'tooltip',
            'data-html' => 'true',
            'data-template' => $template,
            'data-original-title' => "&nbsp;"
        ]);
    }
}
