<?php

use common\components\grid\UserSelect2Column;
use common\models\search\UserProfileSearch;
use common\models\UserProfile;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $userProfileSearchModel UserProfileSearch */
/* @var $userProfileDataProvider yii\data\ActiveDataProvider */

Pjax::begin([
    'id' => 'pjax-call-recording-disabled-user-profile',
    'timeout' => 5000,
]);

echo GridView::widget([
    'dataProvider' => $userProfileDataProvider,
    'filterModel' => $userProfileSearchModel,
    'columns' => [
        [
            'class' => UserSelect2Column::class,
            'attribute' => 'up_user_id',
            'relation' => 'upUser',
            'format' => 'username',
            'placeholder' => 'Select User'
        ],
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'contentOptions' => ['style' => 'width: 90px;'],
            'buttons' => [
                'view' => static function ($url, UserProfile $model) {
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/employee/update', 'id' => $model->up_user_id], ['data-pjax' => 0,]);
                }
            ],
        ],
    ],
]);

Pjax::end();
