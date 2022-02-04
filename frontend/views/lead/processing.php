<?php

use common\components\grid\project\ProjectColumn;
use dosamigos\datepicker\DatePicker;
use src\access\ListsAccess;
use src\helpers\lead\LeadHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;
use common\models\Lead;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $isAgent bool */


$this->title = 'Processing Queue';

$lists = new ListsAccess(Yii::$app->user->id);

$this->params['breadcrumbs'][] = $this->title;

$timeNow = time();

$bundle = \frontend\assets\TimerAsset::register($this);
?>

<style>
.dropdown-menu {
    z-index: 1010 !important;
}
</style>
<h1><i class="fa fa-spinner"></i> <?= Html::encode($this->title)?></h1>
<div class="lead-index">

    <?php Pjax::begin(['timeout' => 5000]); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]);?>
    <?= $this->render('_search_processing', ['model' => $searchModel]); ?>

    <?php

    $gridColumns = [
        [
            'attribute' => 'id',
            'label' => 'Lead ID',
            'value' => static function (Lead $model) {
                return $model->id;
            },
            'options' => [
                'style' => 'width:80px'
            ]
        ],

        [
            'class' => ProjectColumn::class,
            'attribute' => 'project_id',
            'relation' => 'project'
        ],

        [
            'attribute' => 'created',
            'label' => 'Pending Time',
            'value' => static function (Lead $model) {
                return Yii::$app->formatter->asRelativeDt($model->created);
            },
            'options' => [
                'style' => 'width:180px'
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
                    'placeholder' => 'Choose Date'
                ],
            ]),
        ],

        [
            'attribute' => 'l_type',
            'value' => static function (Lead $model) {
                return $model->l_type ? '<span class="label label-default" style="font-size: 13px">' . $model::TYPE_LIST[$model->l_type] . '</span>' : ' - ';
            },
            'format' => 'raw',
            'filter' => Lead::TYPE_LIST,
        ],

        [
            'attribute' => 'status',
            'value' => static function (Lead $model) use ($timeNow) {
                $statusValue = $model->getStatusName(true);
                $reasonValue =  $model->getLastReasonFromLeadFlow();

                if ($model->isSnooze()) {
                    $statusValue .= '<br>' . LeadHelper::displaySnoozeFor($model, $timeNow);
                }

                if ($reasonValue) {
                    $reasonValue = '<span>' . $reasonValue . '</span>';
                }

                return $statusValue . '<br>' . $reasonValue;
            },
            'format' => 'raw',
            'filter' => Lead::getProcessingStatuses(),
            'options' => [
                'style' => 'width:200px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ],

        ],

        [
            'header' => 'Client',
            'format' => 'raw',
            'value' => static function (Lead $lead) {
                return $lead->getClientFormatted();
            },
            'options' => [
                'style' => 'width:160px'
            ]
        ],

        [
            'label' => 'Comm',
            'value' => static function (Lead $lead) {
                return $lead->getCommunicationInfo(false, false);
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],
        [
            'value' => static function (Lead $lead): ?string {
                if ($lead->minLpp && $lead->minLpp->lpp_expiration_dt) {
                    return LeadHelper::displayLeadPoorProcessingTimer($lead->minLpp->lpp_expiration_dt, $lead->minLpp->lppLppd->lppd_name);
                }
                return '-';
            },
            'format' => 'raw',
            'label' => 'Extra Timer'
        ],

        [
            'attribute' => 'Request Details',
            'value' => static function (Lead $lead) {
                return $lead->getFlightDetailsPaxFormatted();
            },
            'format' => 'raw'
        ],


        [
            'attribute' => 'Quotes',
            'value' => static function (Lead $model) {
                return $model->getQuoteInfoFormatted();
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],
        [
            'header' => 'Agent',
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => static function (Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
            },
            'filter' => $lists->getEmployees(),
            'visible' => $lists->getEmployees()
        ],


        [
            'attribute' => 'l_last_action_dt',
            'value' => static function (Lead $model) {
                return Yii::$app->formatter->asRelativeDt($model->l_last_action_dt);
            },
            'options' => [
                'style' => 'width:180px'
            ],

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
                    'placeholder' => 'Choose Date'
                ],
            ]),
        ],

        'l_answered:boolean',
//        [
////            'header' => 'Answered',
//            'attribute' => 'l_answered',
////            'value' => static function (Lead $model) {
////                return $model->l_answered ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
////            },
//            'contentOptions' => [
//                'class' => 'text-center'
//            ],
//            'filter' => [1 => 'Yes', 0 => 'No'],
//            'format' => 'raw'
//        ],

        [
            'attribute' => 'expiration_dt',
            'value' => static function (Lead $model) {
                return Yii::$app->formatter->asExpirationDt($model->l_expiration_dt);
            },
            'options' => [
                'style' => 'width:180px'
            ],
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'expiration_dt',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'clearBtn' => true,
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'Choose Date',
                    'readonly' => '1',
                ],
                'clientEvents' => [
                    'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                ],
            ]),
            'format' => 'raw',
        ],

        [
            'header' => 'Task Info',
            'value' => static function (Lead $model) {
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

//        [
//            'header' => 'Checklist',
//            'value' => static function (\common\models\Lead $model) {
//                return '<small style="font-size: 10px">' . $model->getChecklistInfo($model->employee_id) . '</small>';
//            },
//            'format' => 'raw',
//            'contentOptions' => [
//                'class' => 'text-left'
//            ],
//            //'visible' => ! $isAgent,
//            'options' => [
//                'style' => 'width:170px'
//            ]
//        ],

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
//        [
//            'label' => 'Rating',
//            'contentOptions' => [
//                'style' => 'width: 90px;',
//                'class' => 'text-center'
//            ],
//            'options' => [
//                'class' => 'text-right'
//            ],
//            'value' => static function (\common\models\Lead $model) {
//                return Lead::getRating2($model->rating);
//            },
//            'format' => 'raw'
//        ],

//        [
//            'attribute' => 'l_init_price',
//            //'format' => 'raw',
//            'value' => function (\common\models\Lead $model) {
//                return $model->l_init_price ? number_format($model->l_init_price, 2) . ' $' : '-';
//            },
//            'contentOptions' => [
//                'class' => 'text-right'
//            ],
//            'visible' => ! $isAgent
//        ],


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

    echo \yii\grid\GridView::widget([
        'id' => 'lead-processing-gv',
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

    $js = <<<JS
  $('.enable-timer-lpp').each( function (i, e) {
      let seconds = $(e).attr('data-seconds');
      if (seconds < 0) {
          var params = {format: '%H:%M:%S', seconds: Math.abs(seconds)};
      } else {
          var params = {format: '%H:%M:%S', countdown: true, duration: seconds + 's'};
      }
      $(e).timer(params).timer('start');
  });
JS;
    $this->registerJs($js, \yii\web\View::POS_READY);

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
    'position' => \yii\web\View::POS_END,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
