<?php

use common\models\Setting;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $systemSettingsDataProvider yii\data\ActiveDataProvider */

Pjax::begin([
    'id' => 'pjax-call-recording-disabled-system',
    'timeout' => 5000,
]);

echo GridView::widget([
    'dataProvider' => $systemSettingsDataProvider,
    'columns' => [
        's_name',
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'contentOptions' => ['style' => 'width: 90px;'],
            'buttons' => [
                'view' => static function ($url, Setting $model) {
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/setting/view', 'id' => $model->s_id], ['data-pjax' => 0,]);
                }
            ],
        ],
    ],
]);

Pjax::end();
