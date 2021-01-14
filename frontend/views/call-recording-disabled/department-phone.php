<?php

use common\models\DepartmentPhoneProject;
use common\models\search\DepartmentPhoneProjectSearch;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $departmentPhoneProjectSearchModel DepartmentPhoneProjectSearch */
/* @var $departmentPhoneProjectDataProvider yii\data\ActiveDataProvider */

Pjax::begin([
    'id' => 'pjax-call-recording-disabled-department-phone',
    'timeout' => 5000,
]);

echo GridView::widget([
    'dataProvider' => $departmentPhoneProjectDataProvider,
    'filterModel' => $departmentPhoneProjectSearchModel,
    'columns' => [
        [
            'label' => 'Phone',
            'class' => \common\components\grid\PhoneSelect2Column::class,
            'attribute' => 'dpp_phone_list_id',
            'relation' => 'phoneList',
        ],
        [
            'class' => \common\components\grid\project\ProjectColumn::class,
            'attribute' => 'dpp_project_id',
            'relation' => 'dppProject',
        ],
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'contentOptions' => ['style' => 'width: 90px;'],
            'buttons' => [
                'view' => static function ($url, DepartmentPhoneProject $model) {
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/department-phone-project/view', 'id' => $model->dpp_id], ['data-pjax' => 0,]);
                }
            ],
        ],
    ],
]);

Pjax::end();
