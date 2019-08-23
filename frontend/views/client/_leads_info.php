<?php

use common\models\Employee;
use yii\grid\GridView;
use yii\helpers\Html;
use common\models\Lead;
use common\models\Reason;
use common\models\LeadFlow;

/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="row">
    <div class="col-md-12">
        <h4>Leads</h4>

        <?php
        $gridColumns = [
            [
                'attribute' => 'id',
                'value' => function (Lead $model) {
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
            [
                'attribute' => 'status',
                'value' => function (Lead $model) {
                    $statusValue = $model->getStatusName(true);
                    if ($model->isTrash()) {
                        $reason = Reason::find()->where([
                            'lead_id' => $model->id
                        ])
                            ->orderBy([
                                'id' => SORT_DESC
                            ])
                            ->one();
                        if ($reason) {
                            $statusValue .= ' <span data-toggle="tooltip" data-placement="top"
                                title="' . Html::encode($reason->reason) . '"><i class="fa fa-warning"></i></span>';
                        }
                    }
                    $statusLog = LeadFlow::find()->where([
                        'lead_id' => $model->id,
                        'status' => $model->status
                    ])
                        ->orderBy([
                            'id' => SORT_DESC
                        ])
                        ->one();

                    if ($statusLog) {
                        $statusValue .= '<br><br><span class="label label-default">' . Yii::$app->formatter->asDatetime(strtotime($statusLog->created)) . '</span>';
                        $statusValue .= '<br>' . Yii::$app->formatter->asRelativeTime(strtotime($statusLog->created)) . '';
                    }

                    return $statusValue;
                },
                'format' => 'raw',
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
                'value' => function (Lead $model) {
                    return Yii::$app->formatter->asRelativeTime(strtotime($model->created));
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'project_id',
                'value' => function (Lead $model) {
                    return $model->project ? $model->project->name : '-';
                },
            ],
            [
                'attribute' => 'cabin',
                'value' => function (Lead $model) {
                    return Lead::getCabin($model->cabin) ?: '-';
                },
            ],
            [
                'label' => 'Pax',
                'value' => function (Lead $model) {
                    return '<i class="fa fa-male"></i> <span title="adult">' . $model->adults . '</span> / <span title="child">' . $model->children . '</span>
        / <span title="infant">' . $model->infants . '</span>';
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'header' => 'Quotes',
                'value' => function (Lead $model) {
                    if ($model->quotesCount) {
                        /** @var Employee $user */
                        $user = Yii::$app->user->identity;
                        if ($user->isAgent()) {
                            return $model->quotesCount;
                        }
                        return Html::a($model->quotesCount, ['quotes/index', 'QuoteSearch[lead_id]' => $model->id], ['target' => '_blank', 'data-pjax' => 0]);
                    }
                    return '-';
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'header' => 'Segments',
                'value' => function (Lead $model) {
                    $segmentData = [];
                    foreach ($model->leadFlightSegments as $sk => $segment) {
                        $segmentData[] = ($sk + 1) . '. <code>' . Html::a($segment->origin . ' <i class="fa fa-long-arrow-right"></i> '
                                . $segment->destination, [
                                'lead-flight-segment/view',
                                'id' => $segment->id
                            ], [
                                'target' => '_blank',
                                'data-pjax' => 0
                            ]) . '</code>';
                    }
                    return implode('<br>', $segmentData);
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
                'value' => function (Lead $model) {
                    foreach ($model->leadFlightSegments as $sk => $segment) {
                        return date('d-M-Y', strtotime($segment->departure));
                    }
                    return '';
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
                'attribute' => 'employee_id',
                'format' => 'raw',
                'value' => function (Lead $model) {
                    return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
                },
            ],
            [
                'attribute' => 'created',
                'value' => function (Lead $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                },
                'format' => 'raw'
            ],

        ];

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
        ]);

        ?>
    </div>
</div>
