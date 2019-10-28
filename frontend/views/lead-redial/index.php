<?php

use common\models\Employee;
use common\models\Lead;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\LeadQcall;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadQcallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Redial';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;
$userIsFreeForCall = $user->isCallFree();

?>
    <div class="lead-qcall-list">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-md-12">

                <div id="loading" style="text-align:center; display: none">
                    <img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"/>
                </div>

                <div id="redial-call-box-wrapper">
                    <div id="redial-call-box">
                        <div class="text-center badge badge-warning call-status" style="font-size: 35px">
                            <span id="text-status-call">Ready</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <p></p>

        <?php Pjax::begin(['id' => 'lead-redial-pjax', 'enablePushState' => false, 'enableReplaceState' => true]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => static function (LeadQcall $model, $index, $widget, $grid) {
                if (!$model->lqcLead->isCallProcessing()) {
                    return ['class' => 'danger'];
                }
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'label' => 'Status',
                    'value' => static function (LeadQcall $model) {
                        return $model->lqcLead->getStatusName(true);
                    },
                    'format' => 'raw',
                ],
//                [
//                    'label' => 'Call status',
//                    'value' => static function (LeadQcall $model) {
//                        return Lead::CALL_STATUS_LIST[$model->lqcLead->l_call_status_id] ?? '-';
//                    },
//                    'format' => 'raw',
//                ],
                [
                    'attribute' => 'lqcLead.project_id',
                    'value' => static function (LeadQcall $model) {
                        return $model->lqcLead->project ? '<span class="badge badge-info">' . Html::encode($model->lqcLead->project->name) . '</span>' : '-';
                    },
                    'format' => 'raw',
                    'options' => [
                        'style' => 'width:120px'
                    ],
                ],
                [
                    'attribute' => 'lqc_lead_id',
                    'value' => static function (LeadQcall $model) {
                        return Html::a($model->lqc_lead_id, ['lead/view', 'gid' => $model->lqcLead->gid], ['target' => '_blank', 'data-pjax' => 0]);
                    },
                    'format' => 'raw',
                    'filter' => false,
                    'visible' => !$user->isAgent(),
                ],
                [
                    'attribute' => 'lqcLead.pending',
                    'label' => 'Pending Time',
                    'value' => static function (LeadQcall $model) {

                        $createdTS = strtotime($model->lqcLead->created);

                        $diffTime = time() - $createdTS;
                        $diffHours = (int)($diffTime / (60 * 60));


                        $str = ($diffHours > 3 && $diffHours < 73) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                        $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqcLead->created));

                        return $str;
                    },
                    'options' => [
                        'style' => 'width:160px'
                    ],
                    'format' => 'raw'
                ],
                [
                    'header' => 'Client time',
                    'format' => 'raw',
                    'value' => static function (LeadQcall $model) {
                        return $model->lqcLead->getClientTime2();
                    },
                    'options' => ['style' => 'width:90px'],
                ],
                [
                    'label' => 'Client / Phones',
                    'format' => 'raw',
                    'value' => static function (LeadQcall $model) {
                        $lead = $model->lqcLead;

                        if (!$lead->client) {
                            return '-';
                        }
                        $clientName = $lead->client->first_name . ' ' . $lead->client->last_name;
                        if ($clientName === 'Client Name') {
                            $clientName = '- - - ';
                        } else {
                            $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                        }

                        $str = $clientName . '<br>';
                        $str .= $lead->client->clientPhones ? '<i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', ArrayHelper::map($lead->client->clientPhones, 'phone', 'phone')) . '' : '';

                        return $str;
                    },
                ],
                [
                    'label' => 'Depart',
                    'value' => static function (LeadQcall $model) {
                        $lead = $model->lqcLead;
                        if (!$lead) {
                            return '-';
                        }
                        foreach ($model->lqcLead->leadFlightSegments as $sk => $segment) {
                            return date('d-M-Y', strtotime($segment->departure));
                        }
                        return '-';
                    },
                ],
                [
                    'header' => 'Segments',
                    'value' => static function (LeadQcall $model) {
                        $lead = $model->lqcLead;
                        if (!$lead) {
                            return '-';
                        }
                        $segmentData = [];
                        foreach ($lead->leadFlightSegments as $sk => $segment) {
                            $segmentData[] = ($sk + 1) . '. <code>' . Html::a($segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination, [
                                    'lead-flight-segment/view',
                                    'id' => $segment->id
                                ], [
                                    'target' => '_blank',
                                    'data-pjax' => 0
                                ]) . '</code>';
                        }
                        $segmentStr = implode('<br>', $segmentData);
                        return '' . $segmentStr . '';
                    },
                    'format' => 'raw',
                    'visible' => !$user->isAgent(),
                ],
                [
                    'label' => 'Pax',
                    'value' => static function (LeadQcall $model) {
                        $lead = $model->lqcLead;
                        if (!$lead) {
                            return '-';
                        }
                        $str = '<i class="fa fa-male"></i> <span title="adult">' . $lead->adults . '</span> / <span title="child">' . $lead->children . '</span> / <span title="infant">' . $lead->infants . '</span><br>';
                        //$str .= $model->getCommunicationInfo();
                        return $str;
                    },
                    'format' => 'raw',
                    'visible' => !$user->isAgent(),
                ],
                [
                    'attribute' => 'cabin',
                    'value' => static function (LeadQcall $model) {
                        $lead = $model->lqcLead;
                        if (!$lead) {
                            return '-';
                        }
                        return $lead->getCabinClassName();
                    },
                    'filter' => Lead::CABIN_LIST
                ],
//                [
//                    'label' => 'Out Calls',
//                    'value' => static function (LeadQcall $model) {
//                        $cnt = $model->lqcLead->getCountCalls(\common\models\Call::CALL_TYPE_OUT);
//                        return $cnt ?: '-';
//                    },
//                    'options' => [
//                        'style' => 'width:60px'
//                    ],
//                    'contentOptions' => [
//                        'class' => 'text-center'
//                    ],
//                    //'format' => 'raw'
//                ],
                [
                    'attribute' => 'attempts',
                    'filter' => false,
                ],
//                'lqc_weight',
//                [
//                    'attribute' => 'lqc_dt_from',
//                    'value' => static function (LeadQcall $model) {
//                        return $model->lqc_dt_from ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqc_dt_from)) : '-';
//                    },
//                    'format' => 'raw'
//                ],
//
//                [
//                    'attribute' => 'lqc_dt_to',
//                    'value' => static function (LeadQcall $model) {
//                        return $model->lqc_dt_to ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqc_dt_to)) : '-';
//                    },
//                    'format' => 'raw'
//                ],

//                [
//                    'label' => 'Duration',
//                    'value' => static function (LeadQcall $model) {
//                        return Yii::$app->formatter->asDuration(strtotime($model->lqc_dt_to) - strtotime($model->lqc_dt_from));
//                    },
//                ],
//
//                [
//                    'label' => 'Deadline',
//                    'value' => static function (LeadQcall $model) {
//                        $timeTo = strtotime($model->lqc_dt_to);
//                        return time() <= $timeTo ? Yii::$app->formatter->asDuration($timeTo - time()) : 'deadline';
//                    },
//                ],
//
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{call}',
                    'buttons' => [
                        'call' => static function ($url, LeadQcall $model) use ($userIsFreeForCall) {
                            return Html::button('<i class="fa fa-phone"></i> Call', [
                                'class' => 'btn btn-primary btn-xs lead-redial-btn',
                                'disabled' => ($model->lqcLead->isCallProcessing() || !$userIsFreeForCall) ? 'disabled' : false,
                                'data-url' => Url::to(['lead-redial/redial']),
                                'data-gid' => $model->lqcLead->gid,
                            ]);
                        }
                    ]
                ],
            ],
        ]); ?>

        <?php Pjax::end(); ?>

    </div>

<?php

$js = <<<JS

function loadRedialCallBoxBlock(type, url, data) {
    $("#redial-call-box").html('');
    $("#loading").show();
    $.ajax({
        type: type,
        url: url,
        data: data
    })
    .done(function(data) {
        $("#loading").hide();
        $("#redial-call-box-wrapper").html(data);
    })
    .fail(function() {
        $("#loading").hide();
        new PNotify({title: "Lead redial", type: "error", text: 'Error', hide: true});
    })
}

$("body").on("click", ".lead-redial-btn", function(e) {
    loadRedialCallBoxBlock('post', $(this).data('url'), {gid: $(this).data('gid')});
});

JS;

$this->registerJs($js);

$js = <<<JS

function reloadCallFunction() {
    $.pjax.reload({container: '#lead-redial-pjax', async: false});
}

JS;

$this->registerJs($js, $this::POS_END);
