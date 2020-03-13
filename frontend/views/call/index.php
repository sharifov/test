<?php

use common\models\Call;
use common\models\Employee;
use dosamigos\datepicker\DatePicker;
use sales\yii\grid\call\CallDurationColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call List';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;

if($user->isAdmin()) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId($user->id);
    $projectList = \common\models\Project::getListByUser($user->id);
}

?>
<div class="call-index">
    <h1><i class="fa fa-phone"></i> <?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['timeout' => 10000]); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?php /*= Html::a('Create Call', ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => function (\common\models\Call $model, $index, $widget, $grid) {
            if ((int) $model->c_call_type_id === \common\models\Call::CALL_TYPE_OUT) {
                if ($model->isStatusBusy() || $model->isStatusNoAnswer()) {
                    return ['class' => 'danger'];
                } elseif ($model->isStatusRinging() || $model->isStatusQueue()) {
                    return ['class' => 'warning'];
                } elseif ($model->isStatusCompleted()) {
                    // return ['class' => 'success'];
                }
            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'c_id',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_id;
                },
                'options' => ['style' => 'width: 80px']
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {cancel}',
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
                    'update' => static function ($model, $key, $index) use ($user) {
                        return $user->isAdmin();
                    },

                    'delete' => static function ($model, $key, $index) use ($user) {
                        return $user->isAdmin();
                    },

                    'cancel' => static function (Call $model, $key, $index) use ($user) {
                        return $user->isAdmin() && $model->isIn() && ($model->isStatusIvr() || $model->isStatusQueue() || $model->isStatusRinging() || $model->isStatusInProgress());
                    },
                ],
                'buttons' => [
                    'cancel' => static function ($url, Call $model) {
                        return Html::a('<i class="fa fa-close text-danger"></i>', ['call/cancel', 'id' => $model->c_id], [
                            //'class' => 'btn btn-primary btn-xs take-processing-btn',
                            'title' => 'Cancel Call',
                            'data-pjax' => 0,
                            'data' => [
                                'confirm' => 'Are you sure you want Cancel this Call?',
                                'id' => $model->c_id
                                //'method' => 'post',
                            ],
                        ]);
                    }
                ],
            ],
            [
                'attribute' => 'c_project_id',
                'value' => static function (\common\models\Call $model) {
                    return $model->cProject ? '<span class="badge badge-info">' . Html::encode($model->cProject->name) . '</span>' : '-';
                },
                'format' => 'raw',
                'filter' => $projectList
            ],
            [
                'attribute' => 'c_created_user_id',
                'value' => static function (\common\models\Call $model) {
                    return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                },
                'filter' => $userList,
                'format' => 'raw'
            ],

            [
                'attribute' => 'c_status_id',
                'value' => static function (\common\models\Call $model) {
                    return $model->getStatusLabel();
                },
                'format' => 'raw',
                'filter' => \common\models\Call::STATUS_LIST
            ],

            [
                'attribute' => 'c_created_dt',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt), 'php: Y-m-d [H:i:s]')  : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'c_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ],
                ]),
            ],


            /*[
                'attribute' => 'c_created_dt',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . date('Y-m-d H:i:s', strtotime($model->c_created_dt))  : '-';
                },
                'format' => 'raw',

            ],*/

            /*[
                'attribute' => 'c_recording_url',
                'value' => static function (\common\models\Call $model) {
                    return  $model->c_recording_url ? '<audio controls="controls" style="width: 350px; height: 25px"><source src="'.$model->c_recording_url.'" type="audio/mpeg"> </audio>' : '-';
                },
                'format' => 'raw'
            ],*/

            ['class' => CallDurationColumn::class],

            //'c_recording_duration',

            /*[
                'label' => 'Record Link',
                'value' => static function (\common\models\Call $model) {
                    return  $model->c_recording_url ? Html::a('Link', $model->c_recording_url, ['target' => '_blank']) : '-';
                },
                'format' => 'raw'
            ],*/

            //'c_is_new:boolean',
            //'c_com_call_id',
            [
                'attribute' => 'c_call_sid',
                'value' => static function (\common\models\Call $model) {
                    return  $model->c_call_sid ? '<small>' . $model->c_call_sid . '</small>' : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'c_parent_call_sid',
                'value' => static function (\common\models\Call $model) {
                    return  $model->c_parent_call_sid ? '<small>' . $model->c_parent_call_sid . '</small>' : '-';
                },
                'format' => 'raw'
            ],
            //'c_call_sid',
            //'c_parent_call_sid',

            [
                'attribute' => 'c_call_type_id',
                'value' => static function (\common\models\Call $model) {
                    return $model->getCallTypeName();
                },
                'filter' => \common\models\Call::CALL_TYPE_LIST
            ],

            [
                'attribute' => 'c_source_type_id',
                'value' => static function (\common\models\Call $model) {
                    return $model->getSourceName();
                },
                'filter' => \common\models\Call::SOURCE_LIST
            ],

            //'c_project_id',



            //'c_lead_id',
            [
                'attribute' => 'c_lead_id',
                'value' => static function (\common\models\Call $model) {
                    return  $model->c_lead_id ? Html::a($model->c_lead_id, ['lead/view', 'gid' => $model->cLead->gid, ['target' => '_blank', 'data-pjax' => 0]]) : '-';
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'c_case_id',
                'value' => static function (\common\models\Call $model) {
                    return  $model->c_case_id ? Html::a($model->c_case_id, ['cases/view', 'gid' => $model->cCase->cs_gid], ['target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw'
            ],
            [
                'label' => 'Department',
                'attribute' => 'c_dep_id',
                'value' => static function (Call $model) {
                    return $model->cDep ? $model->cDep->dep_name : '-';
                },
            ],

            [
                'label' => 'UserGroups',
                //'attribute' => 'c_dep_id',
                'value' => static function (Call $model) {
                    $userGroupList = [];
                    if ($model->cugUgs) {
                        foreach ($model->cugUgs as $userGroup) {
                            $userGroupList[] =  '<span class="label label-info"><i class="fa fa-users"></i> ' . Html::encode($userGroup->ug_name) . '</span>';
                        }
                    }
                    return $userGroupList ? implode(' ', $userGroupList) : '-';
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'c_client_id',
                'value' => static function (\common\models\Call $model) {
                    return  $model->c_client_id ?: '-';
                },
            ],

            'c_from',
            'c_to',
            //'c_call_status',
            //'c_forwarded_from',
            //'c_caller_name',
            //'c_parent_call_sid',
            'c_call_duration',
            //'c_price:currency',
            /*[
                'attribute' => 'c_price',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_price ? '$'.number_format($model->c_price, 5) : '-';
                },
            ],*/
            //'c_recording_url:url',

            //'c_sequence_number',

            //'c_created_user_id',



            //'c_created_dt',

            /*[
                'attribute' => 'c_updated_dt',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],*/




            //'c_updated_dt',

            //'c_error_message',

            //'c_is_deleted:boolean',


        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>