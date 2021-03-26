<?php

namespace sales\helpers\attribute;

use Yii;
use yii\bootstrap\Html;
use yii\db\ActiveRecord;

/**
 * Class AttributeHelper
 */
class AttributeHelper
{
    public static function showField(ActiveRecord $model, string $field): string
    {
        if (!$model->hasProperty($field)) {
            throw new \InvalidArgumentException('Model ' . get_class($model) . ' not has property ' . $field);
        }

        $label = $model->getAttributeLabel($field);
        if (!$model->{$field}) {
            return $label . ': ' . Yii::$app->formatter->nullDisplay;
        }
        return $label . ': ' . Html::encode($model->{$field});
    }
}
