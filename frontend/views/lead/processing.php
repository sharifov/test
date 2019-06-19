<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $isAgent bool */


$this->title = 'Processing Queue';

if (Yii::$app->user->identity->canRole('admin')) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    $projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
}

$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.dropdown-menu {
	z-index: 1010 !important;
}
</style>
<h1><i class="fa fa-spinner"></i> <?=\yii\helpers\Html::encode($this->title)?></h1>
<div class="lead-index">

    <?php Pjax::begin(); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>
    <?= $this->render('_search_processing', ['model' => $searchModel]); ?>

    <?php

    $gridColumns = [
        [
            'attribute' => 'id',
            'label' => 'Lead ID',
            'value' => function (\common\models\Lead $model) {
                return $model->id;
            },
            'options' => [
                'style' => 'width:80px'
            ]
        ],


        [
            'attribute' => 'project_id',
            'value' => function (\common\models\Lead $model) {
                return $model->project ? '<span class="badge badge-info">' . $model->project->name . '</span>' : '-';
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:120px'
            ],
            'filter' => $projectList,
        ],


        [
            'attribute' => 'created',
            'label' => 'Pending Time',
            'value' => function (\common\models\Lead $model) {

                $createdTS = strtotime($model->created);

                $diffTime = time() - $createdTS;
                $diffHours = (int) ($diffTime / (60 * 60));


                $str = ($diffHours > 3 && $diffHours < 73 ) ? $diffHours.' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));

                return $str;
            },
            'options' => [
                'style' => 'width:160px'
            ],
            'format' => 'raw'
        ],

        /*[
            'attribute' => 'created',
            'label' => 'Pending Time',
            'value' => function (\common\models\Lead $model) {
                return Yii::$app->formatter->asRelativeTime(strtotime($model->created)); //Lead::getPendingAfterCreate($model->created);
            },
            'format' => 'raw'
        ],

        [
            'attribute' => 'created',
            'value' => function (\common\models\Lead $model) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
            },
            'format' => 'raw',
            'filter' => false

        ],*/

        [
            // 'attribute' => 'client_id',
            'header' => 'Client',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {

                if ($model->client) {
                    $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                    if ($clientName === 'Client Name') {
                        $clientName = '- - - ';
                    } else {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                    }


                    $str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                    $str .= $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';


                    $clientName.= '<br>'. $str;

                } else {
                    $clientName = '-';
                }


                $str = '<br><br>';
                $str .= '<span title="Calls Out / In"><i class="fa fa-phone success"></i> '. $model->getCountCalls(\common\models\Call::CALL_TYPE_OUT) .'/'.  $model->getCountCalls(\common\models\Call::CALL_TYPE_IN) .'</span> | ';
                $str .= '<span title="SMS Out / In"><i class="fa fa-comments info"></i> '. $model->getCountSms(\common\models\Sms::TYPE_OUTBOX) .'/'.  $model->getCountCalls(\common\models\Sms::TYPE_INBOX) .'</span> | ';
                $str .= '<span title="Email Out / In"><i class="fa fa-envelope danger"></i> '. $model->getCountEmails(\common\models\Email::TYPE_OUTBOX) .'/'.  $model->getCountEmails(\common\models\Email::TYPE_INBOX) .'</span>';


                return $clientName . $str;
            },
            'options' => [
                'style' => 'width:160px'
            ]
        ],
        /*[
            'attribute' => 'clientTime',
            'label' => 'Client Time',
            'value' => function ($model) {
                return Lead::getClientTime($model['id']);
            },
            'format' => 'raw'
        ],*/

        [
            //'attribute' => 'clientTime',
            'header' => 'Client time',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                return $model->getClientTime2();
            },
            //'options' => ['style' => 'width:80px'],
            //'filter' => \common\models\Employee::getList()
        ],

        [
            'attribute' => 'Request Details',
            'content' => function (\common\models\Lead $model) {
                $content = '';
                $content .= $model->getFlightDetails();
                $content .= ' (<i class="fa fa-male"></i> x' . ($model->adults .'/'. $model->children .'/'. $model->infants) . ')<br/>';

                $content .= sprintf('<strong>Cabin:</strong> %s', $model->getCabinClassName());

                return $content;
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'Quotes',
            'value' => function (\common\models\Lead $model) {
                $quotes = $model->getQuoteSendInfo();
                return sprintf('Total: <strong>%d</strong> <br> Sent: <strong>%d</strong>', ($quotes['send_q'] + $quotes['not_send_q']), $quotes['send_q']);
            },
            'format' => 'raw'
        ],
        [
            'header' => 'Agent',
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
            },
            'filter' => $userList,
            'visible' => ! $isAgent
        ],
        [
            'attribute' => 'status',
            'value' => function (\common\models\Lead $model) {
                $statusValue = $model->getStatusName(true);
                $reasonValue =  $model->getLastReason();

                if($reasonValue) {
                    $reasonValue = '<br><pre>'.$reasonValue.'</pre>';
                }

                return $statusValue.'<br>'.$reasonValue;
            },
            'format' => 'raw',
            'filter' => \common\models\Lead::getProcessingStatuses(),
            'options' => [
                'style' => 'width:200px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ],

        ],
        /*[
            'attribute' => 'last_activity',
            'label' => 'Last Activity',
            'value' => function (\common\models\Lead $model) {
                return Lead::getLastActivity($model->getLastActivityByNote());
            },
            'format' => 'raw'
        ],*/

        /*[
            'attribute' => 'updated',
            'label' => 'Last Update',
            'value' => function (\common\models\Lead $model) {
                return '<span title="'.Yii::$app->formatter->asDatetime(strtotime($model->updated)).'">'.Yii::$app->formatter->asRelativeTime(strtotime($model->updated)).'</span>';
            },
            'format' => 'raw'
        ],*/


        [
            'attribute' => 'l_last_action_dt',
            //'label' => 'Last Update',
            'value' => function (\common\models\Lead $model) {
                return $model->l_last_action_dt ? '<b>'.Yii::$app->formatter->asRelativeTime(strtotime($model->l_last_action_dt)).'</b><br>' .
                    Yii::$app->formatter->asDatetime(strtotime($model->l_last_action_dt)) : $model->l_last_action_dt;
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'l_last_action_dt',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' =>'Choose Date'
                ],
            ]),
        ],

        /*[
            'attribute' => 'reason',
            'label' => 'Reason',
            'contentOptions' => [
                'style' => 'max-width: 250px;'
            ],
            'value' => function (\common\models\Lead $model) {
                return $model->getLastReason();
            },
            'format' => 'raw'
        ],*/

        [
            'header' => 'Answered',
            'attribute' => 'l_answered',
            'value' => function (\common\models\Lead $model) {
                return $model->l_answered ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'filter' => [1 => 'Yes', 0 => 'No'],
            'format' => 'raw'
        ],

        [
            'header' => 'Grade',
            'attribute' => 'l_grade',
            'value' => function (\common\models\Lead $model) {
                return $model->l_grade;
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'visible' => ! $isAgent
        ],

        [
            'header' => 'Task Info',
            'value' => function (\common\models\Lead $model) {
                return '<small style="font-size: 10px">' . Lead::getTaskInfo2($model->id) . '</small>';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-left'
            ],
            'visible' => ! $isAgent,
            'options' => [
                'style' => 'width:170px'
            ]
        ],

        [
            'header' => 'Checklist',
            'value' => function (\common\models\Lead $model) {
                return '<small style="font-size: 10px">' . $model->getChecklistInfo($model->employee_id) . '</small>';
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-left'
            ],
            //'visible' => ! $isAgent,
            'options' => [
                'style' => 'width:170px'
            ]
        ],

        /*[
            'label' => 'Countdown',
            'contentOptions' => [
                'style' => 'width: 115px;'
            ],
            'value' => function (\common\models\Lead $model) {
                return Lead::getSnoozeCountdown($model->id, $model->snooze_for);
            },
            'format' => 'raw'
        ],*/
        /*[
            'attribute' => 'project_id',
            'value' => function(\common\models\Lead $model) {
                return $model->project ? $model->project->name : '-';
            },
            'filter' => $projectList,
            'visible' => ! $isAgent
        ],*/
        [
            'label' => 'Rating',
            'contentOptions' => [
                'style' => 'width: 90px;',
                'class' => 'text-center'
            ],
            'options' => [
                'class' => 'text-right'
            ],
            'value' => function (\common\models\Lead $model) {
                return Lead::getRating2($model->rating);
            },
            'format' => 'raw'
        ],

        [
            'attribute' => 'l_init_price',
            //'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                return $model->l_init_price ? number_format($model->l_init_price, 2) . ' $': '-';
            },
            'contentOptions' => [
                'class' => 'text-right'
            ],
            'visible' => ! $isAgent
        ],

        /*[
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, \common\models\Lead $model, $key) {

                    $buttons = '';

                    $buttons .= Html::a('<i class="fa fa-search"></i> View', ['lead/view', 'gid' => $model->gid], [
                        'class' => 'btn btn-info btn-xs',
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => 'View lead'
                    ]);

                    if (Yii::$app->user->id === $model->employee_id && $model->status === Lead::STATUS_ON_HOLD) {

                        $buttons .= Html::a('Take', ['lead/take', 'gid' => $model->gid], ['class' => 'btn btn-primary btn-xs take-btn', 'data-pjax' => 0]);
                    }

                    if (Yii::$app->user->id != $model->employee_id && in_array($model->status, [Lead::STATUS_ON_HOLD, Lead::STATUS_PROCESSING])) {

                        $buttons .= ' ' . Html::a('Take Over', ['lead/take', 'gid' => $model->gid, 'over' => true], [
                            'class' => 'btn btn-primary btn-xs take-processing-btn',
                            'data-pjax' => 0,
                            'data-status' => $model->status
                        ]);
                    }

                    return $buttons;
                }
            ]
        ]*/

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}<br>{take-over}',
            'controller' => 'lead',

            'visibleButtons' => [

                /*'view' => function ($model, $key, $index) use ($isAdmin) {
                    return $isAdmin;
                },*/

                /*'take' => function ($model, $key, $index) use ($isAdmin) {
                    return $isAdmin;
                },*/

                'take-over' => function (Lead $model, $key, $index) {
                    return Yii::$app->user->id !== $model->employee_id && in_array($model->status, [Lead::STATUS_ON_HOLD, Lead::STATUS_PROCESSING]);
                },


            ],

            'buttons' => [
                'view' => function ($url, Lead $model) {
                    return Html::a('<i class="glyphicon glyphicon-search"></i> View Lead', [
                        'lead/view',
                        'gid' => $model->gid
                    ], [
                        'class' => 'btn btn-info btn-xs',
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => 'View',
                    ]);
                },
                'take-over' => function ($url, Lead $model) {
                    return Html::a('<i class="fa fa-download"></i> Take Over', ['lead/take', 'gid' => $model->gid, 'over' => true], [
                        'class' => 'btn btn-primary btn-xs take-processing-btn',
                        'data-pjax' => 0,
                        'data-status' => $model->status,
                        /*'data' => [
                            'confirm' => 'Are you sure you want to take over this Lead?',
                            //'method' => 'post',
                        ],*/
                    ]);
                }
            ],
        ]

    ];

    ?>
<?php

/*echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'toolbar' => false,
    'pjax' => false,
    'striped' => true,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'floatHeader' => true,
    'floatHeaderOptions' => [
        'scrollingTop' => 20
    ],
    //'panel' => [
    //    'type' => GridView::TYPE_PRIMARY,
    //    'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Processing</h3>'
    //],

    'rowOptions' => function (Lead $model) {
        if ($model->status === Lead::STATUS_PROCESSING && Yii::$app->user->id == $model->employee_id) {
            return [
                'class' => 'highlighted'
            ];
        }
    }

]);*/


    echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,


        'rowOptions' => function (Lead $model) {
            if ($model->status === Lead::STATUS_PROCESSING && Yii::$app->user->id == $model->employee_id) {
                return [
                    'class' => 'highlighted'
                ];
            }
        }
    ]);

?>




    <?php Pjax::end(); ?>


</div>

<?php
$js = <<<JS
    $('.take-processing-btn').click(function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if ($.inArray($(this).data('status'), [2, 8]) != -1) {
            var editBlock = $('#modal-error');
            editBlock.find('.modal-body').html('');
            editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
                editBlock.modal('show');
            });
        } else {
            window.location = url;
        }
    });

/*$(document).on('pjax:end', function() {
    setClienTime();
});*/

JS;
$this->registerJs($js);

/*$this->registerJsFile('/js/moment.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
$this->registerJsFile('/js/moment-timezone-with-data.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);*/

$this->registerJsFile('/js/jquery.countdown-2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);