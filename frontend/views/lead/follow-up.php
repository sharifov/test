<?php

use dosamigos\datepicker\DatePicker;
use sales\access\EmployeeProjectAccess;
use sales\ui\user\ListsAccess;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $isAgent bool */


$this->title = 'Follow Up Queue';

$lists = new ListsAccess(Yii::$app->user->id);

$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.dropdown-menu {
	z-index: 1010 !important;
}
</style>
<h1>
    <i class="fa fa-recycle"></i>
	<?=\yii\helpers\Html::encode($this->title)?>

</h1>
<div class="lead-index">

    <?php Pjax::begin(); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>
    <?= $this->render('_search_follow_up', ['model' => $searchModel]); ?>


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
            'attribute' => 'created',
            'label' => 'Pending Time',
            'value' => function (\common\models\Lead $model) {
                return Yii::$app->formatter->asRelativeTime(strtotime($model->created)); //Lead::getPendingAfterCreate($model->created);
            },
            'format' => 'raw',
            'filter' => false
        ],

        [
            'attribute' => 'created',
            'value' => function (\common\models\Lead $model) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
            },
            'format' => 'raw',
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

                $str = '';
                /*$str .= '<span title="Calls Out / In"><i class="fa fa-phone success"></i> '. $model->getCountCalls(\common\models\Call::CALL_TYPE_OUT) .'/'.  $model->getCountCalls(\common\models\Call::CALL_TYPE_IN) .'</span> | ';
                $str .= '<span title="SMS Out / In"><i class="fa fa-comments info"></i> '. $model->getCountSms(\common\models\Sms::TYPE_OUTBOX) .'/'.  $model->getCountCalls(\common\models\Sms::TYPE_INBOX) .'</span> | ';
                $str .= '<span title="Email Out / In"><i class="fa fa-envelope danger"></i> '. $model->getCountEmails(\common\models\Email::TYPE_OUTBOX) .'/'.  $model->getCountEmails(\common\models\Email::TYPE_INBOX) .'</span>';*/

                return $clientName.'<br/>'.$model->getClientTime2().$str;
            },
            'options' => [
                'style' => 'width:160px'
            ]
        ],

        [
            'label' => 'Communication',
            'value' => function (\common\models\Lead $model) {
                $str = '';
                $str .= '<span title="Calls Out / In"><i class="fa fa-phone success"></i> '. $model->getCountCalls(\common\models\Call::CALL_TYPE_OUT) .'/'.  $model->getCountCalls(\common\models\Call::CALL_TYPE_IN) .'</span> | ';
                $str .= '<span title="SMS Out / In"><i class="fa fa-comments info"></i> '. $model->getCountSms(\common\models\Sms::TYPE_OUTBOX) .'/'.  $model->getCountCalls(\common\models\Sms::TYPE_INBOX) .'</span> | ';
                $str .= '<span title="Email Out / In"><i class="fa fa-envelope danger"></i> '. $model->getCountEmails(\common\models\Email::TYPE_OUTBOX) .'/'.  $model->getCountEmails(\common\models\Email::TYPE_INBOX) .'</span>';
                return $str;
            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
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

       /*  [
            //'attribute' => 'client_id',
            'header' => 'Client time',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                return ;
            },
            'options' => ['style' => 'width:100px'],
            //'filter' => \common\models\Employee::getList()
        ], */

        [
            'attribute' => 'Request Details',
            'content' => function (\common\models\Lead $model) {
                $content = '';
                $content .= $model->getFlightDetails();
                $pax = '<span title="adult"><i class="fa fa-male"></i> '. $model->adults .'</span> / <span title="child"><i class="fa fa-child"></i> ' . $model->children . '</span> / <span title="infant"><i class="fa fa-info"></i> ' . $model->infants.'</span>';

                $content .= '<br/>'.$pax.'<br/>';

                $content .= sprintf('<strong>Cabin:</strong> %s', $model->getCabinClassName());

                return $content;
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'Quotes ',
            'value' => function (\common\models\Lead $model) {
                $quotes = $model->getQuoteSendInfo();
                return sprintf('Total: <strong>%d</strong> <br> Sent: <strong>%d</strong>', ($quotes['send_q'] + $quotes['not_send_q']), $quotes['send_q']);
            },
            'format' => 'raw'
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
            'label' => 'Last Activity',
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

        [
            'attribute' => 'reason',
            'label' => 'Reason',
            'contentOptions' => [
                'style' => 'text-align:center;'
            ],
            'value' => function (\common\models\Lead $model) {
                return '<span style="cursor:help;" class="label label-warning" title="'.Html::encode($model->getLastReason()).'">&nbsp;<i class="fa fa-info-circle"></i>&nbsp;</span>';
            },
            'format' => 'raw'
        ],

        [
            'header' => 'Answ.',
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
            'header' => 'Task Info',
            'value' => function (\common\models\Lead $model) {
                return '<small style="font-size: 10px">' . Lead::getTaskInfo2($model->id) . '</small>';
            },
            'format' => 'html',
            'contentOptions' => [
                'class' => 'text-left'
            ],
            'visible' => ! $isAgent,
            'options' => [
                'style' => 'width:140px'
            ]
        ],
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
            'attribute' => 'project_id',
            'value' => function (\common\models\Lead $model) {
                return $model->project ? $model->project->name : '-';
            },
            'filter' => $lists->getProjects(),
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, \common\models\Lead $model, $key) {

                    $buttons = '';

                    $buttons .= Html::a('Take', [
                        'lead/take',
                        'gid' => $model->gid
                    ], [
                        'class' => 'btn btn-primary btn-xs take-btn',
                        'data-pjax' => 0
                    ]);

                    $buttons .= Html::a('<i class="fa fa-search"></i>', ['lead/view', 'gid' => $model->gid], [
                        'class' => 'btn btn-info btn-xs',
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => 'View lead'
                    ]);

                    return $buttons;
                }
            ]
        ]
    ];

    ?>
<?php
echo GridView::widget([
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
    /*'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Processing</h3>'
    ],*/

    'rowOptions' => function (Lead $model) {
        if ($model->status === Lead::STATUS_PROCESSING && Yii::$app->user->id == $model->employee_id) {
            return [
                'class' => 'highlighted'
            ];
        }

        /*if (in_array($model->status, [
            Lead::STATUS_ON_HOLD,
            Lead::STATUS_BOOKED,
            Lead::STATUS_FOLLOW_UP
        ])) {
            $now = new \DateTime();
            $departure = $model->getDeparture();

            $diff = ! empty($departure) ? $now->diff(new \DateTime($departure)) : $now->diff(new \DateTime($departure));
            $diffInSec = $diff->s + ($diff->i * 60) + ($diff->h * 3600) + ($diff->d * 86400) + ($diff->m * 30 * 86400) + ($diff->y * 12 * 30 * 86400);
            // if departure <= 7 days
            if ($diffInSec <= (7 * 24 * 60 * 60)) {
                return [
                    'class' => 'success'
                ];
            }
        }*/
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