<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
//use kartik\grid\GridView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $isAgent bool */

$statusList = \common\models\Lead::STATUS_LIST;

if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    if($isAgent) {
        $statusList = \common\models\Lead::STATUS_LIST;
        unset($statusList[\common\models\Lead::STATUS_PENDING]);
    }
}

?>
<div class="client-update">
    <?//=\yii\helpers\VarDumper::dumpAsString($client->attributes)?>

    <div class="row">
        <div class="col-md-6">
            <?= \yii\widgets\DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'first_name',
                    'middle_name',
                    'last_name',
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= \yii\widgets\DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Phones',
                        'value' => function(\common\models\Client $model) {

                            $phones = $model->clientPhones;
                            $data = [];
                            if($phones) {
                                foreach ($phones as $k => $phone) {
                                    $data[] = '<i class="fa fa-phone"></i> <code>'.Html::encode($phone->phone).'</code>'; //<code>'.Html::a($phone->phone, ['client-phone/view', 'id' => $phone->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                                }
                            }

                            $str = implode('<br>', $data);
                            return ''.$str.'';
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                    ],

                    [
                        'label' => 'Emails',
                        'value' => function(\common\models\Client $model) {

                            $emails = $model->clientEmails;
                            $data = [];
                            if($emails) {
                                foreach ($emails as $k => $email) {
                                    $data[] = '<i class="fa fa-envelope"></i> <code>'.Html::encode($email->email).'</code>';
                                }
                            }

                            $str = implode('<br>', $data);
                            return ''.$str.'';
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                    ],

                    //'created',
                    //'updated',

                    [
                        'attribute' => 'created',
                        'value' => function(\common\models\Client $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'updated',
                        'value' => function(\common\models\Client $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                        },
                        'format' => 'html',
                    ],
                ],
            ]) ?>
        </div>
    </div>



    <div class="row">
        <div class="col-md-12">
            <h4>Leads</h4>

        <?php

        $gridColumns = [
            // ['class' => 'yii\grid\SerialColumn'],


            [
                'attribute' => 'id',
                'value' => function (\common\models\Lead $model) {
                    return Html::a('<i class="fa fa-file-o"></i> ' . $model->id, [
                        'lead/view', 'gid' => $model->gid
                    ], [
                        'data-pjax' => 0,
                        'target' => '_blank'
                    ]);
                },
                'format' => 'raw',
                'options' => [
                    'style' => 'width:80px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],

            /*[
                'attribute' => 'uid',
                'options' => [
                    'style' => 'width:100px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],*/


            //'employee_id',
            //'status',
            [
                'attribute' => 'status',
                'value' => function (\common\models\Lead $model) {
                    $statusValue = $model->getStatusName(true);

                    if ($model->status === \common\models\Lead::STATUS_TRASH) {
                        $reason = \common\models\Reason::find()->where([
                            'lead_id' => $model->id
                        ])
                            ->orderBy([
                                'id' => SORT_DESC
                            ])
                            ->one();
                        if ($reason) {
                            $statusValue .= ' <span data-toggle="tooltip" data-placement="top" title="' . Html::encode($reason->reason) . '"><i class="fa fa-warning"></i></span>';
                        }
                    }

                    $statusLog = \common\models\LeadFlow::find()->where([
                        'lead_id' => $model->id,
                        'status' => $model->status
                    ])
                        ->orderBy([
                            'id' => SORT_DESC
                        ])
                        ->one();

                    if ($statusLog) {
                        // $statusValue .= '<br><span class="label label-default">'.Yii::$app->formatter->asDatetime(strtotime($statusLog->created)).'</span>';
                        $statusValue .= '<br><br><span class="label label-default">' . Yii::$app->formatter->asDatetime(strtotime($statusLog->created)) . '</span>';
                        $statusValue .= '<br>' . Yii::$app->formatter->asRelativeTime(strtotime($statusLog->created)) . '';
                    }

                    return $statusValue;
                },
                'format' => 'raw',
                'filter' => \common\models\Lead::STATUS_LIST,
                'options' => [
                    'style' => 'width:100px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]

            ],
            [
                'attribute' => 'created',
                'label' => 'Pending Time',
                'value' => function (\common\models\Lead $model) {
                    return Yii::$app->formatter->asRelativeTime(strtotime($model->created)); // Lead::getPendingAfterCreate($model->created);
                },
                //'visible' => !$isAgent,
                'format' => 'raw'
            ],
            [
                'attribute' => 'project_id',
                'value' => function (\common\models\Lead $model) {
                    return $model->project ? $model->project->name : '-';
                },
                'filter' => \common\models\Project::getList()
            ],

            // 'project_id',
            // 'source_id',
            /*[
                'attribute' => 'source_id',
                'value' => function (\common\models\Lead $model) {
                    return $model->source ? $model->source->name : '-';
                },
                'filter' => \common\models\Source::getList(),
                'visible' => ! $isAgent
            ],

            [
                'attribute' => 'trip_type',
                'value' => function (\common\models\Lead $model) {
                    return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                },
                'filter' => \common\models\Lead::TRIP_TYPE_LIST
            ],*/

            [
                'attribute' => 'cabin',
                'value' => function (\common\models\Lead $model) {
                    return \common\models\Lead::getCabin($model->cabin) ?? '-';
                },
                'filter' => \common\models\Lead::CABIN_LIST
            ],

            // 'trip_type',
            // 'cabin',
            // 'adults',

            [
                'label' => 'Pax',
                'value' => function (\common\models\Lead $model) {
                    return '<i class="fa fa-male"></i> <span title="adult">'. $model->adults .'</span> / <span title="child">' . $model->children . '</span> / <span title="infant">' . $model->infants.'</span>';
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],


            [
                'header' => 'Quotes',
                'value' => function (\common\models\Lead $model) use ($isAgent) {
                    return $model->quotesCount ? ($isAgent ? $model->quotesCount : Html::a($model->quotesCount, ['quotes/index', 'QuoteSearch[lead_id]' => $model->id], ['target' => '_blank', 'data-pjax' => 0])) : '-';
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],

            [
                'header' => 'Segments',
                'value' => function (\common\models\Lead $model) {

                    $segments = $model->leadFlightSegments;
                    $segmentData = [];
                    if ($segments) {
                        foreach ($segments as $sk => $segment) {
                            $segmentData[] = ($sk + 1) . '. <code>' . Html::a($segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination, [
                                    'lead-flight-segment/view',
                                    'id' => $segment->id
                                ], [
                                    'target' => '_blank',
                                    'data-pjax' => 0
                                ]) . '</code>';
                        }
                    }

                    $segmentStr = implode('<br>', $segmentData);
                    return '' . $segmentStr . '';
                    // return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-left'
                ],
                'options' => [
                    'style' => 'width:140px'
                ]
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
            // 'children',
            // 'infants',
            // 'notes_for_experts:ntext',

            // 'updated',
            // 'request_ip',
            // 'request_ip_detail:ntext',

            [
                'attribute' => 'employee_id',
                'format' => 'raw',
                'value' => function (\common\models\Lead $model) {
                    return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
                },
                'filter' => $userList
            ],

            // 'rating',
            // 'called_expert',
            /*
             * [
             * 'attribute' => 'discount_id',
             * 'options' => ['style' => 'width:100px'],
             * 'contentOptions' => ['class' => 'text-center'],
             * ],
             */
            // 'offset_gmt',
            // 'snooze_for',
            // 'created',
            [
                'attribute' => 'created',
                'value' => function (\common\models\Lead $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                },
                'format' => 'raw'
            ],
            // 'created:date',


            // 'bo_flight_id',


        ];

        ?>



        <?php


        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => false, //$isAgent ? false : $searchModel,
            'columns' => $gridColumns,
        ]);

        ?>
    </div>
    </div>

</div>
