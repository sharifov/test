<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\LogForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Logging';
$template = <<<HTML
<div class="pagination-container row" style="margin-bottom: 10px;">
    <div class="col-sm-3" style="padding-top: 20px;">
        {summary}
    </div>
    <div class="col-sm-9" style="text-align: right;">
       {pager}
    </div>
</div>
<div class="table-responsive">
    {items}
</div>
HTML;
?>

<div class="panel panel-default">
    <div class="panel-heading">Logging</div>
    <div class="panel-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout' => $template,
            'columns' => [
                [
                    'attribute' => 'category',
                    'value' => 'category',
                    'contentOptions' => ['style' => 'width: 200px;'],
                ],
                [
                    'attribute' => 'log_time',
                    'value' => 'log_time',
                    'format' => 'datetime',
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'log_time',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-M-yyyy'
                        ]
                    ]),
                    'contentOptions' => ['style' => 'width: 200px;'],
                ],
                [
                    'attribute' => 'message',
                    'value' => function ($model) {
                        return \yii\helpers\StringHelper::truncate($model->message, 100, '...', null, true);
                    },
                    'format' => 'ntext',
                    'contentOptions' => [
                        'style' => 'max-width: 600px;'
                    ],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            /**
                             * @var $model \common\models\Log
                             */
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to([
                                'settings/view-log',
                                'id' => $model->id
                            ]));
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
