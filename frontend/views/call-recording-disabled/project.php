<?php

use common\models\Project;
use common\models\search\ProjectSearch;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $projectSearchModel ProjectSearch */
/* @var $projectDataProvider yii\data\ActiveDataProvider */

Pjax::begin([
    'id' => 'pjax-call-recording-disabled-project',
    'timeout' => 5000,
]);

echo GridView::widget([
    'dataProvider' => $projectDataProvider,
    'filterModel' => $projectSearchModel,
    'columns' => [
        'name:projectName',
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'contentOptions' => ['style' => 'width: 90px;'],
            'buttons' => [
                'view' => static function ($url, Project $model) {
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/project/view', 'id' => $model->id], ['data-pjax' => 0,]);
                }
            ],
        ],
    ],
]);

Pjax::end();
