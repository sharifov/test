<?php

use sales\model\callLog\entity\callLog\CallLog;
use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\callLog\entity\callLog\search\CallLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Logs';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="call-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cl_id',
            'cl_parent_id',
            ['class' => DateTimeColumn::class, 'attribute' => 'cl_call_created_dt', 'format' => 'byUserDateTimeWithSeconds'],
            ['class' => DateTimeColumn::class, 'attribute' => 'cl_call_finished_dt'],
            'cl_duration',
            ['class' => \sales\model\callLog\grid\columns\RecordingUrlColumn::class],
            ['class' => \sales\model\callLog\grid\columns\CallLogStatusColumn::class],
            ['class' => \sales\model\callLog\grid\columns\CallLogTypeColumn::class],
            ['class' => \sales\model\callLog\grid\columns\CallLogCategoryColumn::class],
            ['class' => BooleanColumn::class, 'attribute' => 'cl_is_transfer'],
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'cl_project_id',
                'relation' => 'project',
            ],
            [
                'class' => \common\components\grid\department\DepartmentColumn::class,
                'attribute' => 'cl_department_id',
                'relation' => 'department',
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cl_user_id',
                'relation' => 'user',
            ],
            'cl_client_id:client',
            [
                'label' => 'Lead Id',
                'attribute' => 'lead_id',
                'value' => static function (CallLog $log) {
                    return $log->callLogLead ? $log->callLogLead->lead : null;
                },
                'format' => 'lead'
            ],
            [
                'label' => 'Case Id',
                'attribute' => 'case_id',
                'value' => static function (CallLog $log) {
                    return $log->callLogCase ? $log->callLogCase->case : null;
                },
                'format' => 'case'
            ],
            'cl_phone_from',
            'cl_phone_to',
            [
                'class' => \common\components\grid\PhoneSelect2Column::class,
                'attribute' => 'cl_phone_list_id',
                'relation' => 'phoneList',
            ],
            'cl_price',
            [
                'attribute' => 'clq_queue_time',
                'label' => 'Queue duration',
                'value' => static function (CallLog $model) {
                    if (!$model->queue) {
                        return null;
                    }
                    return $model->queue->clq_queue_time;
                }
            ],
            [
                'attribute' => 'clq_access_count',
                'label' => 'Queue access count',
                'value' => static function (CallLog $model) {
                    if (!$model->queue) {
                        return null;
                    }
                    return $model->queue->clq_access_count;
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => static function ($url, CallLog $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/call-log/view', 'id' => $model->cl_id], [
                            'target' => '_blank',
                            'data-pjax' => 0,
                            'title' => 'View',
                        ]);
                    },
                    'update' => static function ($url, CallLog $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/call-log/update', 'id' => $model->cl_id], [
                            'target' => '_blank',
                            'data-pjax' => 0,
                            'title' => 'Update',
                        ]);
                    },
                    'delete' => static function ($url, CallLog $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/call-log/delete', 'id' => $model->cl_id], [
                            'data-pjax' => 0,
                            'title' => 'Delete',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this Call Log?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ]
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
