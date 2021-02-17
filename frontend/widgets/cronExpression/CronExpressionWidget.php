<?php

namespace frontend\widgets\cronExpression;

class CronExpressionWidget extends \yii\base\Widget
{
    public $model;

    public $attribute;

    public function run()
    {
        return $this->render('cron_expression', [
            'model' => $this->model,
            'attribute' => $this->attribute
        ]);
    }
}
