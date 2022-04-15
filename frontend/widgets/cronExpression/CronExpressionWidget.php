<?php

namespace frontend\widgets\cronExpression;

use yii\helpers\VarDumper;

class CronExpressionWidget extends \yii\base\Widget
{
    public $model;
    public $attribute;
    public $options = [];

    public function run()
    {

        return $this->render('cron_expression', [
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => $this->options,
        ]);
    }
}
