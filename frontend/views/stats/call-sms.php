<?php

use common\models\Employee;
use common\components\grid\DateTimeColumn;
use sales\helpers\call\CallHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\bootstrap4\Modal;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */
/* @var $dataProviderCommunication \yii\data\ActiveDataProvider */
/* @var $datetime_start string */
/* @var $datetime_end string */

$bundle = \frontend\assets\TimelineAsset::register($this);
$this->title = 'Stats Calls & SMS';

$js = <<<JS
    //google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);


/** @var Employee $user */
$user = Yii::$app->user->identity;

if ($user->isAdmin() || $user->isQa()) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId($user->id);
    $projectList = \common\models\Project::getListByUser($user->id);
}

?>
    <div class="stats-call-sms">
        <h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>
        <?php Pjax::begin(['scrollTo' => 0]); ?>
        <div class="card card-default">
            <div class="card-header"><i class="fa fa-bar-chart"></i> Call & SMS Stats</div>
            <div class="card-body">
                <div class="row">
                    <?php $form = ActiveForm::begin([
                        'action' => ['call-sms'],
                        'method' => 'get',
                        'options' => [
                            'data-pjax' => 1
                        ],
                    ]); ?>

                    <div class="col-md-3">
                        <?php
                        echo  \kartik\daterange\DateRangePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'date_range',
                            'useWithAddon' => true,
                            'presetDropdown' => true,
                            'hideInput' => true,
                            'convertFormat' => true,
                            'startAttribute' => 'datetime_start',
                            'endAttribute' => 'datetime_end',
                            'pluginOptions' => [
                                'timePicker' => true,
                                'timePickerIncrement' => 1,
                                'timePicker24Hour' => true,
                                'locale' => [
                                    'format' => 'Y-m-d H:i',
                                    'separator' => ' - '
                                ],
                                'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                            ]
                        ]);
                        ?>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Show result', ['class' => 'btn btn-success']) ?>
                        <?php //= Html::resetButton('Reset', ['class' => 'btn btn-default'])?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>

                <?= GridView::widget([
                    'dataProvider' => $dataProviderCommunication,
                    'filterModel' => $searchModel,
                    /*'rowOptions' => function (\common\models\Employee $model, $index, $widget, $grid) {
                        if ($model->deleted) {
                            return ['class' => 'danger'];
                        }
                    },*/
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'options' => ['style' => 'width:100px'],
                        ],

                        [
                            'label' => 'Obj Id',
                            'attribute' => 'id',
                            'value' => static function ($model) {
                                return $model['id'];
                            },
                            'options' => ['style' => 'width:100px'],
                            //'format' => 'raw',
                        ],

                        /*[
                            'attribute' => 'id',
                            'contentOptions' => ['class' => 'text-center'],
                            'options' => ['style' => 'width:60px'],
                        ],
                        [
                            'attribute' => 'username',
                            'value' => static function (\common\models\Employee $model) {
                                return Html::tag('i', '', ['class' => 'fa fa-user']).' '.Html::encode($model->username);
                            },
                            'format' => 'raw',
                            //'contentOptions' => ['title' => 'text-center'],
                            'options' => ['style' => 'width:180px'],
                        ],

                        [
                            //'attribute' => 'username',
                            'label' => 'Role',
                            'value' => static function (\common\models\Employee $model) {
                                $roles = $model->getRoles();
                                return $roles ? implode(', ', $roles) : '-';
                            },
                            'options' => ['style' => 'width:150px'],
                            //'format' => 'raw'
                        ],*/



                        [
                            'label' => 'Communication Type',
                            'attribute' => 'communication_type_id',
                            'value' => static function ($model) {
                                return \common\models\search\CommunicationSearch::COMM_TYPE_LIST[$model['communication_type_id']] ?? '-';
                            },
                            //'format' => 'raw',
                            'filter' => \common\models\search\CommunicationSearch::COMM_TYPE_LIST
                        ],

                        [
                            'label' => 'Type / Status',
                            'value' => static function ($model) {
                                $type = '';
                                $statusTitle = '';

                                if ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {
                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        $type = $call->getCallTypeName();
                                        $statusTitle = $call->getStatusName(); //'INIT';

                                        /*if ($call->c_call_type_id == \common\models\Call::CALL_TYPE_IN) {
                                            $type = 'Incoming';
                                        } else if ($call->c_call_type_id == \common\models\Call::CALL_TYPE_OUT) {
                                            $type = 'Outgoing';
                                        }*/
                                    }
                                } elseif ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {
                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        $type = $sms->getTypeName();
                                        $statusTitle = $sms->getStatusName(); //'INIT';

                                        /*if ($sms->s_type_id == \common\models\Sms::TYPE_INBOX) {
                                            $type = 'Incoming';
                                        } else if ($sms->s_type_id == \common\models\Sms::TYPE_OUTBOX) {
                                            $type = 'Outgoing';
                                        }*/
                                    }
                                }
                                return $type . ' / ' . $statusTitle . '';
                            },
                            'format' => 'raw',
                            //'filter' => $user->isAdmin() ? \common\models\UserGroup::getList() : $user->getUserGroupList()
                        ],

                        [
                            'label' => 'Created Date',
                            'class' => DateTimeColumn::class,
                            'attribute' => 'created_dt'
                        ],

                        /*[
                            'label' => 'Created Date',
                            'attribute' => 'created_dt',
                            'value' => static function ($model) {
                                return $model['created_dt'] ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model['created_dt']), 'php: Y-m-d [H:i:s]')  : '-';
                            },
                            'format' => 'raw',
                            'filter' => DatePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'created_dt',
                                'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd'
                                ]
                            ]),
                        ],*/

                        [
                            'label' => 'Agent Phone',
                            'value' => static function ($model) {
                                $phone = '-';
                                if ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {
                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if ($call->c_call_status == \common\models\Call::CALL_TYPE_IN) {
                                            $phone = $call->c_from;
                                        } else {
                                            $phone = $call->c_to;
                                        }
                                    }
                                } elseif ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {
                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if ($sms->s_type_id == \common\models\Sms::TYPE_INBOX) {
                                            $phone = $sms->s_phone_from;
                                        } elseif ($sms->s_type_id == \common\models\Sms::TYPE_OUTBOX) {
                                            $phone = $sms->s_phone_to;
                                        }
                                    }
                                }

                                return $phone; //$model['lead_id'];
                            },
                            //'format' => 'raw',
                        ],

                        [
                            'label' => 'Agent Name',
                            'attribute' => 'created_user_id',
                            'value' => static function ($model) {
                                $agent = '-';

                                if ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {
                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if ($call->cCreatedUser) {
                                            $agent = $call->cCreatedUser->username;
                                        }
                                    }
                                } elseif ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {
                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if ($sms->sCreatedUser) {
                                            $agent = $sms->sCreatedUser->username;
                                        }
                                    }
                                }

                                return  Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' . Html::encode($agent);
                            },
                            'format' => 'raw',
                            'filter' => $userList
                        ],

                        [
                            'label' => 'Agent Group',
                            'attribute' => 'user_group_id',
                            'value' => static function ($model) {
                                $user = null;

                                if ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {
                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if ($call->cCreatedUser) {
                                            $user = $call->cCreatedUser;
                                        }
                                    }
                                } elseif ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {
                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if ($sms->sCreatedUser) {
                                            $user = $sms->sCreatedUser;
                                        }
                                    }
                                }


                                if ($user) {
                                    $groups = $user->getUserGroupList();
                                    $groupsValueArr = [];

                                    foreach ($groups as $group) {
                                        $groupsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' . Html::encode($group), ['class' => 'label label-default']);
                                    }

                                    $groupsValue = implode(' ', $groupsValueArr);
                                } else {
                                    $groupsValue = '';
                                }

                                return $groupsValue;
                            },
                            'format' => 'raw',
                            'filter' => $user->isAdmin() ? \common\models\UserGroup::getList() : $user->getUserGroupList()
                        ],

                        [
                            'label' => 'Client Phone',
                            'value' => static function ($model) {
                                $phone = '-';


                                if ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {
                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if ($call->c_call_status == \common\models\Call::CALL_TYPE_IN) {
                                            $phone = $call->c_to;
                                        } else {
                                            $phone = $call->c_from;
                                        }
                                    }
                                } elseif ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {
                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if ($sms->s_type_id == \common\models\Sms::TYPE_INBOX) {
                                            $phone = $sms->s_phone_to;
                                        } elseif ($sms->s_type_id == \common\models\Sms::TYPE_OUTBOX) {
                                            $phone = $sms->s_phone_from;
                                        }
                                    }
                                }

                                return $phone;
                            },
                            'format' => 'raw',
                        ],


                        [
                            //'label' => 'Lead Id',
                            'attribute' => 'lead_id',
                            'value' => static function ($model) {
                                $lead = \common\models\Lead::findOne($model['lead_id']);
                                return $lead ? Html::a($model['lead_id'], ['lead/view', 'gid' => $lead->gid], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                            },
                            'format' => 'raw',
                        ],

                        [
                            'label' => 'Project',
                            'attribute' => 'project_id',
                            'value' => static function ($model) {
                                $project = null;

                                if ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {
                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if ($call->cProject) {
                                            $project = $call->cProject;
                                        }
                                    }
                                } elseif ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {
                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if ($sms->sProject) {
                                            $project = $sms->sProject;
                                        }
                                    }
                                }

                                if ($project) {
                                    return $project->name;
                                } else {
                                    return '-';
                                }
                            },
                            'filter' => $projectList
                            //'format' => 'raw',
                        ],

                        [
                            'label' => 'Length',
                            'value' => static function ($model) {
                                $duration = '-';

                                if ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {
                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if ($call->c_call_duration) {
                                            $duration = Yii::$app->formatter->asDuration($call->c_call_duration);
                                        }
                                    }
                                } elseif ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {
                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if ($sms->s_sms_text) {
                                            $duration = mb_strlen($sms->s_sms_text);
                                        }
                                    }
                                }
                                return $duration;
                            },
                            'format' => 'raw',
                        ],

                        [
                            'label' => 'View',
                            'value' => static function ($model) {
                                $view = '-';

                                if ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {
                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call && $call->recordingUrl) {
                                        $view = CallHelper::displayAudioBtn($call->recordingUrl, 'i:s', $call->c_recording_duration);
                                    }
                                } elseif ($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {
                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        $view =  Html::button('<i class="fa fa-search"></i> View', [

                                            'class' => 'btn btn-xs btn-info view_sms',
                                            //'data-toggle' => 'popover',

                                            'title' => strip_tags($sms->s_sms_text),
                                            'data-content' => nl2br($sms->s_sms_text),
                                            //'data-placement' => 'left',

                                            //'data-original-title' => 'Select Emails',
                                        ]);

                                        //$view = Html::a('<i class="fa fa-search"></i> View', '#', ['class' => 'btn btn-xs btn-info']);
                                    }
                                }

                                return $view;
                            },
                            'format' => 'raw',
                        ],

                        /*
                         [
                             'label' => 'Booked',
                             'value' => static function (\common\models\Employee $model) use ($searchModel) {
                                 $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_BOOKED], null, $searchModel->datetime_start, $searchModel->datetime_end);
                                 return $cnt ? Html::a($cnt, ['lead-flow/index',
                                     'LeadFlowSearch[employee_id]' => $model->id,
                                     'LeadFlowSearch[status]' => \common\models\Lead::STATUS_BOOKED,
                                     'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                     'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                                 ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                             },
                             'format' => 'raw',
                         ],*/

                    ]
                ])
?>


            </div>
        </div>
        <?php Pjax::end(); ?>

    </div>

<?php Modal::begin([
        'id' => 'modal-sms-preview',
        'title' => 'SMS preview',
        'size' => Modal::SIZE_LARGE
])?>
<?php Modal::end()?>

<?php
$js = <<<JS
    $('body').on('click', '.view_sms', function() {
        let data = $(this).data('content');
        let previewPopup = $('#modal-sms-preview');
        previewPopup.find('.modal-body').html(data);
        previewPopup.modal('show');
    });
JS;

$this->registerJs($js);
