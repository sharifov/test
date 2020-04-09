<?php

use dosamigos\datepicker\DatePicker;
use sales\access\EmployeeProjectAccess;
use sales\access\ListsAccess;
use sales\formatters\client\ClientTimeFormatter;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $isAgent bool */


$this->title = 'Processing Queue';

$lists = new ListsAccess(Yii::$app->user->id);

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
            'value' => static function (\common\models\Lead $model) {
                return $model->id;
            },
            'options' => [
                'style' => 'width:80px'
            ]
        ],

        [
            'class' => \common\components\grid\project\ProjectColumn::class,
            'attribute' => 'project_id',
            'relation' => 'project'
        ],

        [
            'attribute' => 'created',
            'label' => 'Pending Time',
            'value' => static function (\common\models\Lead $model) {

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
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'created',
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
            'attribute' => 'created',
            'label' => 'Pending Time',
            'value' => static function (\common\models\Lead $model) {
                return Yii::$app->formatter->asRelativeTime(strtotime($model->created)); //Lead::getPendingAfterCreate($model->created);
            },
            'format' => 'raw'
        ],

        [
            'attribute' => 'created',
            'value' => static function (\common\models\Lead $model) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
            },
            'format' => 'raw',
            'filter' => false

        ],*/

        [
            // 'attribute' => 'client_id',
            'header' => 'Client',
            'format' => 'raw',
            'value' => static function (\common\models\Lead $model) {

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
                $str .= $model->getCommunicationInfo();

                return $clientName . $str;
            },
            'options' => [
                'style' => 'width:160px'
            ]
        ],
        /*[
            'attribute' => 'clientTime',
            'label' => 'Client Time',
            'value' => static function ($model) {
                return Lead::getClientTime($model['id']);
            },
            'format' => 'raw'
        ],*/

        [
            //'attribute' => 'clientTime',
            'header' => 'Client time',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                return ClientTimeFormatter::format($model->getClientTime2(), $model->offset_gmt);
            },
            //'options' => ['style' => 'width:80px'],
            //'filter' => \common\models\Employee::getList()
        ],

        [
            'attribute' => 'Request Details',
            'content' => function (\common\models\Lead $model) {
                $content = '';
                $content .= $model->getFlightDetails();

                if ($model->adults || $model->children || $model->infants) {
                    $content .= ' (<i class="fa fa-male"></i> x' . ($model->adults . '/' . $model->children . '/' . $model->infants) . ')<br/>';
                }

                if ($cabinClassName = $model->getCabinClassName()) {
                    $content .= '<strong>Cabin:</strong> ' . $cabinClassName;
                }

                return $content;
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'Quotes',
            'value' => static function (\common\models\Lead $model) {
                $quotes = $model->getQuoteSendInfo();
                return sprintf('Total: <strong>%d</strong> <br> Sent: <strong>%d</strong>', ($quotes['send_q'] + $quotes['not_send_q']), $quotes['send_q']);
            },
            'format' => 'raw'
        ],
        [
            'header' => 'Agent',
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => static function (\common\models\Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
            },
            'filter' => $lists->getEmployees(),
            'visible' => $lists->getEmployees()
        ],
        [
            'attribute' => 'status',
            'value' => static function (\common\models\Lead $model) {
                $statusValue = $model->getStatusName(true);
                $reasonValue =  $model->getLastReasonFromLeadFlow();

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
            'value' => static function (\common\models\Lead $model) {
                return Lead::getLastActivity($model->getLastActivityByNote());
            },
            'format' => 'raw'
        ],*/

        /*[
            'attribute' => 'updated',
            'label' => 'Last Update',
            'value' => static function (\common\models\Lead $model) {
                return '<span title="'.Yii::$app->formatter->asDatetime(strtotime($model->updated)).'">'.Yii::$app->formatter->asRelativeTime(strtotime($model->updated)).'</span>';
            },
            'format' => 'raw'
        ],*/


        [
            'attribute' => 'l_last_action_dt',
            //'label' => 'Last Update',
            'value' => static function (\common\models\Lead $model) {
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

        [
            'header' => 'Answered',
            'attribute' => 'l_answered',
            'value' => static function (\common\models\Lead $model) {
                return $model->l_answered ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'filter' => [1 => 'Yes', 0 => 'No'],
            'format' => 'raw'
        ],

        [
            'header' => 'Task Info',
            'value' => static function (\common\models\Lead $model) {
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
            'value' => static function (\common\models\Lead $model) {
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
            'value' => static function (\common\models\Lead $model) {
                return Lead::getSnoozeCountdown($model->id, $model->snooze_for);
            },
            'format' => 'raw'
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
            'value' => static function (\common\models\Lead $model) {
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
    $('.take-processing-btn').on('click', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if ($.inArray($(this).data('status'), [2, 8]) != -1) {
            let modal = $('#modal-df');
            $('#modal-df-label').html('Take processing');
            modal.find('.modal-body').html('');
            modal.find('.modal-body').load(url, function( response, status, xhr ) {
                modal.modal('show');
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