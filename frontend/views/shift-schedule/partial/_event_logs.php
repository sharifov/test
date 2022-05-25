<?php

use modules\shiftSchedule\src\entities\userShiftScheduleLog\search\UserShiftScheduleLogSearch;
use modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLog;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var $dataProvider ActiveDataProvider
 * @var $searchModel UserShiftScheduleLogSearch
 * @var $id int
 * @var $this \yii\web\View
 */
Pjax::begin(['id' => 'pjax-event-logs', 'formSelector' => 'UserShiftScheduleLogSearch', 'timeout' => 2000, 'enablePushState' => false, 'clientOptions' => ['method' => 'post', 'data' => [
    'id' => $id,
]]]);

$view = $this;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => false,
    'tableOptions' => [
        'class' => 'table table-striped table-bordered table-responsive'
    ],
    'columns' => [
        [
            'attribute' => 'ussl_id',
        ],
        [
            'header' => 'Who made the changes',
            'attribute' => 'ussl_created_user_id',
            'value' => static function (UserShiftScheduleLog $model) {
                $template = '';
                if ($model->ussl_created_user_id) {
                    $template .= '<i class="fa fa-user"></i> ';
                    $template .= \yii\helpers\Html::encode($model->userCreated->username) . ' (' . $model->ussl_created_user_id . ')';
                }
                $template .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->ussl_created_dt));

                return $template;
            },
            'format' => 'raw',
            'options' => [
                'width' => '15%'
            ]
        ],
        [
            'header' => 'Changed Attributes',
            'attribute' => 'ussl_formatted_attr',
            'value' => static function (UserShiftScheduleLog $model) use ($view) {
                if ($model->ussl_formatted_attr) {
                    return $view->render('_formatted_attributes', [
                        'model' => $model
                    ]);
                }

                return '';
            },
            'filter' => false,
            'enableSorting' => false,
            'format' => 'raw',
            'options' => [
                'width' => '100%'
            ]
        ]
    ],
]);

Pjax::end();
