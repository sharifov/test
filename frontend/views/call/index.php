<?php

use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\Call;
use common\models\ConferenceParticipant;
use common\models\Employee;
use dosamigos\datepicker\DatePicker;
use common\components\grid\call\CallDurationColumn;
use sales\auth\Auth;
use sales\model\callLogFilterGuard\entity\CallLogFilterGuard;
use sales\services\cleaner\form\DbCleanerParamsForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use sales\helpers\phone\MaskPhoneHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var DbCleanerParamsForm $modelCleaner */

$this->title = 'Call List';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;
$pjaxListId = 'pjax-call-index';
?>

<div class="call-index">
    <h1><i class="fa fa-phone"></i> <?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?php /*= Html::a('Create Call', ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>
    <?php if (Auth::can('global/clean/table')) : ?>
        <?php echo $this->render('../clean/_clean_table_form', [
            'modelCleaner' => $modelCleaner,
            'pjaxIdForReload' => $pjaxListId,
        ]); ?>
    <?php endif ?>

    <?php Pjax::begin(['id' => $pjaxListId, 'timeout' => 10000]); ?>
    <?= GridView::widget([
        'id' => 'call-gv',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => function (\common\models\Call $model, $index, $widget, $grid) {
            if ($model->isOut()) {
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
                'template' => '{view} {update} {delete} {cancel} {join}',
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

                    'join' => static function (Call $model, $key, $index) use ($user) {
                        return
                            ((bool)(Yii::$app->params['settings']['voip_conference_base'] ?? false)
                            && Auth::can('/phone/ajax-join-to-conference'))
                            && (int)$model['cp_type_id'] === ConferenceParticipant::TYPE_AGENT
                            && ($model->isIn() || $model->isOut() || $model->isReturn())
                            && $model->isStatusInProgress();
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
                    },
                    'join' => static function ($url, Call $model) {
                        return'<div class="dropdown">
                              <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-phone"></i>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item conference-coach" href="#" onclick="joinListen(\'' . $model->c_call_sid . '\');">Listen</a>
                                <a class="dropdown-item conference-coach" href="#" onclick="joinCoach(\'' . $model->c_call_sid . '\');">Coach</a>
                                <a class="dropdown-item conference-coach" href="#" onclick="joinBarge(\'' . $model->c_call_sid . '\');">Barge</a>
                              </div>
                            </div>';
                    }
                ],
            ],

            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'c_project_id',
                'relation' => 'cProject',
            ],

//            [
//                'attribute' => 'c_project_id',
//                'value' => static function (\common\models\Call $model) {
//                    return $model->cProject ? '<span class="badge badge-info">' . Html::encode($model->cProject->name) . '</span>' : '-';
//                },
//                'format' => 'raw',
//                'filter' => $projectList
//            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'c_created_user_id',
                'relation' => 'cCreatedUser',
                'placeholder' => ''
            ],

//            [
//                'attribute' => 'c_created_user_id',
//                'value' => static function (\common\models\Call $model) {
//                    return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
//                },
//                'filter' => $userList,
//                'format' => 'raw'
//            ],

            [
                'attribute' => 'c_status_id',
                'value' => static function (\common\models\Call $model) {
                    return $model->getStatusLabel();
                },
                'format' => 'raw',
                'filter' => \common\models\Call::STATUS_LIST
            ],
            ['class' => BooleanColumn::class, 'attribute' => 'c_is_transfer'],
            ['class' => DateTimeColumn::class, 'attribute' => 'c_queue_start_dt', 'format' => 'byUserDateTimeWithSeconds'],
            'c_group_id',
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
                'filter' => \common\models\Call::TYPE_LIST
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

//            [
//                'label' => 'UserGroups',
//                //'attribute' => 'c_dep_id',
//                'value' => static function (Call $model) {
//                    $userGroupList = [];
//                    if ($model->cugUgs) {
//                        foreach ($model->cugUgs as $userGroup) {
//                            $userGroupList[] =  '<span class="label label-info"><i class="fa fa-users"></i> ' . Html::encode($userGroup->ug_name) . '</span>';
//                        }
//                    }
//                    return $userGroupList ? implode(' ', $userGroupList) : '-';
//                },
//                'format' => 'raw'
//            ],

            [
                'attribute' => 'c_language_id',
                'value' => static function (\common\models\Call $model) {
                    return  $model->c_language_id ? $model->cLanguage->language_id : '-';
                },
                'filter' => false
            ],

            'c_client_id:client',
//            [
//                'attribute' => 'c_client_id',
//                'value' => static function (\common\models\Call $model) {
//                    return  $model->c_client_id ?: '-';
//                },
//            ],

            //'c_from',
            [
                'attribute' => 'c_from',
                'value' => static function (Call $model) {
                    if ($model->c_call_type_id == $model::CALL_TYPE_IN) {
                        return MaskPhoneHelper::masking($model->c_from);
                    }
                    return $model->c_from;
                }
            ],
            //'c_to',
            [
                'attribute' => 'c_to',
                'value' => static function (Call $model) {
                    if ($model->c_call_type_id == $model::CALL_TYPE_OUT) {
                        return MaskPhoneHelper::masking($model->c_to);
                    }
                    return $model->c_to;
                }
            ],
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

            [
                'attribute' => 'c_stir_status',
                'filter' => \common\models\Call::STIR_STATUS_LIST
            ],
            [
                'attribute' => 'clfg_type',
                'filter' => CallLogFilterGuard::TYPE_LIST,
                'value' => static function (Call $model) {
                    return $model->callLogFilterGuard ? $model->callLogFilterGuard->getTypeName() : null;
                },
            ],
            [
                'attribute' => 'clfg_rate',
                'value' => static function (Call $model) {
                    return $model->callLogFilterGuard->clfg_sd_rate ?? null;
                }
            ],
            [
                'attribute' => 'clfg_redial_status',
                'filter' => Call::STATUS_LIST,
                'value' => static function (Call $model) {
                    return $model->callLogFilterGuard ? $model->callLogFilterGuard->getRedialStatusName() : null;
                },
            ]

        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php
$js = <<<JS
    $(document).on('pjax:success', function() {
        $("html, body").animate({ scrollTop: $('#call-gv').position().top }, 400);
    })
JS;

$this->registerJs($js, $this::POS_END);
?>