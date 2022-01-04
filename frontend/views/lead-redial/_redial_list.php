<?php

use common\models\Employee;
use common\models\Lead;
use common\models\LeadQcall;
use common\models\search\LeadQcallSearch;
use dosamigos\datepicker\DatePicker;
use sales\formatters\client\ClientTimeFormatter;
use kartik\grid\GridView;
use common\components\grid\client\ClientPhonesColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $searchModel common\models\search\LeadQcallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $guard array */
/** @var Employee $user */

?>

<?= GridView::widget([
    'id' => 'redialGrid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'rowOptions' => static function (LeadQcallSearch $model, $index, $widget, $grid) {
//        if (!$model->deadline) {
//            return ['class' => 'danger'];
//        }
        if ($model->l_is_test) {
            return ['class' => 'info'];
        }
    },
    'columns' => [
        [
            'class' => '\kartik\grid\CheckboxColumn',
            'visible' => $user->isAdmin(),
        ],
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => 'Status',
            'attribute' => 'leadStatus',
            'value' => static function (LeadQcall $model) {
                return $model->lqcLead->getStatusName(true);
            },
            'format' => 'raw',
            'filter' => Lead::getStatusList(),
            'visible' => $user->isAdmin(),
        ],
        [
            'attribute' => 'l_call_status_id',
            'label' => 'Call status',
            'value' => static function (LeadQcall $model) {
                return Lead::CALL_STATUS_LIST[$model->lqcLead->l_call_status_id] ?? '-';
            },
            'format' => 'raw',
            'visible' => $user->isAdmin(),
            'filter' =>  Lead::CALL_STATUS_LIST,
        ],
        [
            'label' => 'Project',
            'attribute' => 'projectId',
            'value' => static function (LeadQcall $model) {
                return $model->lqcLead->project ? '<span class="badge badge-info">' . Html::encode($model->lqcLead->project->name) . '</span>' : '-';
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:120px'
            ],
            'filter' => $list->getProjects()
        ],
        [
            'attribute' => 'lqc_lead_id',
            'value' => static function (LeadQcall $model) {
                return Html::a($model->lqc_lead_id, ['lead/view', 'gid' => $model->lqcLead->gid], ['target' => '_blank', 'data-pjax' => 0]);
            },
            'format' => 'raw',
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
            'format' => 'raw',
            'visible' => !$user->isAgent(),
        ],
        [
            'header' => 'Client time',
            'format' => 'raw',
            'value' => static function (LeadQcall $model) {
                return ClientTimeFormatter::dayHoursFormat($model->lqcLead->getClientTime2(), $model->lqcLead->offset_gmt);
            },
            'options' => ['style' => 'width:90px'],
            'visible' => !$user->isAgent(),
        ],
        [
            'attribute' => 'lqc_call_from',
            'visible' => $user->isAdmin(),
        ],
        [
            'class' => ClientPhonesColumn::class,
            'client' => static function (LeadQcall $model) {
                return $model->lqcLead->client ?? null;
            },
            'visible' => !$user->isAgent(),
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
//            'filter' => false,
//          'enableSorting' => false,
            'visible' => !$user->isAgent(),
        ],
        [
            'attribute' => 'lqc_weight',
            'visible' => $user->isAdmin()
        ],
        [
            'attribute' => 'lqc_created_dt',
            'value' => static function (LeadQcall $model) {
                return $model->lqc_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqc_created_dt)) : '-';
            },
            'format' => 'raw',
            'visible' => $user->isAdmin(),
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'lqc_created_dt',
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
            'attribute' => 'lqc_dt_from',
            'value' => static function (LeadQcall $model) {
                return $model->lqc_dt_from ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqc_dt_from)) : '-';
            },
            'format' => 'raw',
            'visible' => $user->isAdmin(),
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'lqc_dt_from',
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
            'attribute' => 'lqc_dt_to',
            'value' => static function (LeadQcall $model) {
                return $model->lqc_dt_to ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqc_dt_to)) : '-';
            },
            'format' => 'raw',
            'visible' => $user->isAdmin(),
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'lqc_dt_to',
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
//                [
//                    'label' => 'Duration',
//                    'value' => static function (LeadQcall $model) {
//                        return Yii::$app->formatter->asDuration(strtotime($model->lqc_dt_to) - strtotime($model->lqc_dt_from));
//                    },
//                ],
        [
            'label' => 'Deadline',
            'value' => static function (LeadQcall $model) {
                /** @var Employee $user */
                $user = Yii::$app->user->identity;
                if ($user->isAgent() || $user->isSupervision()) {
                    if (time() > strtotime($model->lqc_dt_to)) {
                        return 'deadline';
                    }
                    return Yii::$app->formatter->asDuration(strtotime($model->lqc_dt_to) - time());
                }
                return floor((strtotime($model->lqc_dt_to) - time()) / 60);
            },
            'visible' => !$user->isAgent(),
        ],
        [
            'label' => 'Reserved for',
            'value' => static function (LeadQcall $model) {
                if ($model->lqc_reservation_time && (strtotime($model->lqc_reservation_time) > time())) {
                    return date('H:i:s', (strtotime($model->lqc_reservation_time) - time()));
                }
                return '';
            },
        ],
        [
            'label' => 'Reserved by',
            'value' => static function (LeadQcall $model) {
                if (
                    $model->lqc_reservation_time && (strtotime($model->lqc_reservation_time) > time())
                    && ($user = $model->reservationUser)
                ) {
                    return $user->username;
                }
                return '';
            },
        ],
        [
            'label' => 'Is Test',
            'attribute' => 'l_is_test',
            'value' => static function (LeadQcall $model) {
                if ($model->l_is_test) {
                    $label = '<label class="label label-success">True</label>';
                } else {
                    $label = '<label class="label label-danger">False</label>';
                }
                return $label;
            },
            'options' => [
                'style' => 'width:120px'
            ],
            'format' => 'raw',
            'filter' => [
                1 => 'True',
                0 => 'False'
            ],
            'visible' => ($user->checkIfUsersIpIsAllowed() || Yii::$app->request->get('is_test'))
        ],
//        [
//            'class' => 'yii\grid\ActionColumn',
//            'template' => '{call}',
//            'buttons' => [
//                'call' => static function ($url, LeadQcall $model) use ($userIsFreeForCall, $guard) {
//
//                    if ($guard) {
//                        $str = '';
//                        foreach ($guard as $item) {
//                            $str .= '<i class="fa fa-warning warning"></i> ' . $item . '<br />';
//                        }
//                        return $str;
//                    }
//
//                    return Html::button('<i class="fa fa-phone"></i> Call', [
//                        'class' => 'btn btn-primary btn-xs lead-redial-btn',
//                        'disabled' => ($model->lqcLead->isCallProcessing() || !$userIsFreeForCall) ? 'disabled' : false,
//                        'data-url' => Url::to(['lead-redial/redial']),
//                        'data-gid' => $model->lqcLead->gid,
//                    ]);
//                }
//            ]
//        ],
    ],
]);
