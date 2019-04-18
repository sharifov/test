<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
//use kartik\grid\GridView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pending Queue';

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    $projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
}

/*
$this->registerJsFile('/js/moment.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
$this->registerJsFile('/js/jquery.countdown-2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
*/

$this->params['breadcrumbs'][] = $this->title;
?>

<h1>
    <i class="fa fa-briefcase"></i> <?=\yii\helpers\Html::encode($this->title)?>
</h1>

<div class="lead-pending">



    <?php Pjax::begin(); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>

    <?php

    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],

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
            'attribute' => 'source_id',
            'value' => function(\common\models\Lead $model) {
                return $model->source ? $model->source->name : '-';
            },
        ],

        [
            'attribute' => 'pending',
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
            // 'attribute' => 'client_id',
            'header' => 'Client',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {

                if ($model->client) {

                    if($model->client->first_name !== 'ClientName') {
                        $clientName = trim($model->client->first_name . ' ' . $model->client->last_name);
                    } else {
                        $clientName = '';
                    }

                    if ($clientName) {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName).'<br>';
                    }

                    $str = $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                    $str .= $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';

                    $clientName .= $str;
                } else {
                    $clientName = '-';
                }

                return $clientName;
            },
            //'visible' => ! $isAgent,
            'options' => [
                'style' => 'width:160px'
            ]
        ],*/


        /*[
            'attribute' => 'client.name',
            'header' => 'Client name',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                if($model->client) {
                    $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                    if ($clientName === 'Client Name') {
                        $clientName = '- - - ';
                    } else {
                        $clientName = '<i class="fa fa-user"></i> '. Html::encode($clientName);
                    }
                } else {
                    $clientName = '-';
                }

                return $clientName;
            },
            'options' => ['style' => 'width:160px'],
            //'filter' => \common\models\Employee::getList()
        ],*/

        [
            'attribute' => 'client.phone',
            //'header' => 'Client Phones',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model)
            {
                $str = null;
                if($model->client && $model->client->clientPhones) {
                    $str = '<i class="fa fa-phone"></i> ' . implode('<br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone'));
                }
                return $str ?? '-';
            },
            'options' => ['style' => 'width:180px'],
        ],

        [
            'header' => 'Client time',
            'format' => 'raw',
            'value' => function(\common\models\Lead $model) {
                return $model->getClientTime2();
            },
            'options' => ['style' => 'width:90px'],
        ],

        [
            //'attribute' => 'Quotes',
            'label' => 'Calls',
            'value' => function (\common\models\Lead $model) {
                $cnt = $model->getCountCalls();
                return $cnt ?: '-';
            },
            'options' => [
                'style' => 'width:60px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ],
            //'format' => 'raw'
        ],


        [
            'header' => 'Depart',
            'value' => function (\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;

                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        return date('d-M-Y', strtotime($segment->departure));
                    }
                }
                return '-';

            },
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'options' => [
                'style' => 'width:100px'
            ]
        ],

        [
            'header' => 'Segments',
            'value' => function (\common\models\Lead $model) {

                $segments = $model->leadFlightSegments;
                $segmentData = [];
                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        $segmentData[] = ($sk + 1) . '. <small>' . $segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination . '</small>';
                    }
                }

                $segmentStr = implode('<br>', $segmentData);
                return '' . $segmentStr . '';
                // return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
            },
            'format' => 'raw',
            //'visible' => ! $isAgent,
            'contentOptions' => [
                'class' => 'text-left'
            ],
            'options' => [
                'style' => 'width:140px'
            ]
        ],

        [
            'label' => 'Pax',
            'value' => function (\common\models\Lead $model) {
                return '<span title="adult"><i class="fa fa-male"></i> '. $model->adults .'</span> / <span title="child"><i class="fa fa-child"></i> ' . $model->children . '</span> / <span title="infant"><i class="fa fa-info"></i> ' . $model->infants.'</span>';
            },
            'format' => 'raw',
            //'visible' => ! $isAgent,
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'options' => [
                'style' => 'width:100px'
            ]
        ],


        [
            'attribute' => 'cabin',
            'value' => function (\common\models\Lead $model) {
                return \common\models\Lead::getCabin($model->cabin) ?? '-';
            },
            'filter' => false //\common\models\Lead::CABIN_LIST
        ],


        /*[
            'attribute' => 'l_call_status_id',
            'value' => function (\common\models\Lead $model) {
                return \common\models\Lead::CALL_STATUS_LIST[$model->l_call_status_id] ?? '-';
            },
            'filter' => \common\models\Lead::CALL_STATUS_LIST
        ],*/

        [
            'attribute' => 'request_ip',
            'value' => function (\common\models\Lead $model) {
                return $model->request_ip;
            },
        ],

        [
            'attribute' => 'l_pending_delay_dt',
            'value' => function (\common\models\Lead $model) {
                return $model->l_pending_delay_dt ? Yii::$app->formatter->asDatetime(strtotime($model->l_pending_delay_dt)) : '-';
            },
        ],

        [
            'attribute' => 'l_request_hash',
            'value' => function (\common\models\Lead $model) {
                return $model->l_request_hash ?: '-';
            },
        ],

        [
            //'attribute' => 'l_request_hash',
            'label' => 'Duplicate',
            'value' => function (\common\models\Lead $model) {
                return $model->lDuplicateLead ? count($model->lDuplicateLead) : '-';
            },
        ],



        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, \common\models\Lead $model, $key) {
                    $buttons = '';

                    $buttons .= Html::a('<i class="fa fa-search"></i> View', ['lead/view', 'gid' => $model->gid], [
                            'class' => 'btn btn-info btn-xs',
                            'data-pjax' => 0,
                            'target' => '_blank',
                        ]);

                    $buttons .= ' '. Html::a('<i class="fa fa-list-ul"></i> View', ['leads/view', 'id' => $model->id], [
                            'class' => 'btn btn-warning btn-xs',
                            'data-pjax' => 0,
                            'target' => '_blank',
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
    'rowOptions' => function (\common\models\Lead $model) {
        if ($model->l_pending_delay_dt && time() < strtotime($model->l_pending_delay_dt)) {
            return ['class' => 'danger'];
        }

        if (!$model->l_client_time && (time() - strtotime($model->created)) > (\common\models\Lead::PENDING_ALLOW_CALL_TIME_MINUTES * 60)) {
            return ['class' => 'danger'];
        }
    }
]);
?>
<?php Pjax::end(); ?>
</div>


<?php
/*$js = '
function initCountDown()
{
    $("[data-countdown]").each(function() {
      var $this = $(this), finalDate = $(this).data("countdown");
      var elapsedTime = $(this).data("elapsed");

        var seconds = new Date().getTime() + (elapsedTime * 1000);
        $this.countdown(seconds, function(event) {
            $(this).html(event.strftime(\'%H:%M:%S\'));
        });
    });
}

$(document).on(\'pjax:end\', function() {
    initCountDown();
    setClienTime();
});

initCountDown();

';

$this->registerJs($js);*/