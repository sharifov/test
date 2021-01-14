<?php

use common\models\Department;
use common\models\search\DepartmentSearch;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $departmentSearchModel DepartmentSearch */
/* @var $departmentDataProvider yii\data\ActiveDataProvider */

Pjax::begin([
    'id' => 'pjax-call-recording-disabled-department',
    'timeout' => 5000,
]);

echo GridView::widget([
    'dataProvider' => $departmentDataProvider,
    'filterModel' => $departmentSearchModel,
    'columns' => [
        'dep_name',
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'contentOptions' => ['style' => 'width: 90px;'],
            'buttons' => [
                'view' => static function ($url, Department $model) {
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/department/view', 'id' => $model->dep_id], ['data-pjax' => 0,]);
                }
            ],
        ],
    ],
]);

Pjax::end();
