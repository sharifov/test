<?php

namespace common\components\widgets;

use yii\widgets\ActiveForm;

/**
 * Class BaseForm
 *
 * @package common\components\widgets
 *
 * @method BaseField field($model, $attribute, $options = [])
 */
class BaseForm extends ActiveForm
{
    public $fieldClass = BaseField::class;


    public function simpleField($model, $attribute, $options = [])
    {
        return $this->field($model, $attribute, $options)
            ->withoutContainer()
            ->simpleTemplate()
        ;
    }
}
