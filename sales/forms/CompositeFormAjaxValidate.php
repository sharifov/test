<?php

namespace sales\forms;

use Yii;

class CompositeFormAjaxValidate
{

    public static function validate(CompositeForm $form): array
    {
        $form->validate();
        $errors = $form->getFirstErrors();
        foreach ($errors as $attribute => $error) {
            $formatAttribute = self::getInputId($attribute);
            if ($formatAttribute !== $attribute) {
                $errors[$formatAttribute] = $error;
                unset($errors[$attribute]);
            }
        }
        return $errors;
    }

    public static function getInputId($name)
    {
        $charset = Yii::$app ? Yii::$app->charset : 'UTF-8';
        $name = mb_strtolower($name, $charset);
        return str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], $name);
    }
}
