<?php

use common\models\Employee;
use yii\grid\GridView;
use yii\helpers\Html;
use common\models\Lead;
use common\models\LeadFlow;

/* @var $dataProvider yii\data\ActiveDataProvider */

/** @var Employee $user */
$user = Yii::$app->user->identity;

?>

<div class="row">
    <div class="col-md-12">
        <h4>Leads</h4>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'id',
                    'value' => static function (Lead $model) {
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
                    'value' => static function (Lead $model) {
                        $statusValue = $model->getStatusName(true);
                        if ($model->isTrash() && ($lastLeadFlow = $model->lastLeadFlow)) {
                            if ($lastLeadFlow->status === $model->status && $lastLeadFlow->lf_description) {
                                $statusValue .= ' <span data-toggle="tooltip" data-placement="top" title="' . Html::encode($lastLeadFlow->lf_description) . '"><i class="fa fa-warning"></i></span>';
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
                    'value' => static function (Lead $model) {
                        return Yii::$app->formatter->asRelativeTime(strtotime($model->created));
                    },
                    'format' => 'raw'
                ],
                [
                    'label' => 'Project',
                    'attribute' => 'project_id',
                    'value' => static function (Lead $model) {
                        return $model->project ? $model->project->name : '-';
                    },
                ],
                [
                    'attribute' => 'cabin',
                    'value' => static function (Lead $model) {
                        return Lead::getCabin($model->cabin) ?: '-';
                    },
                ],
                [
                    'label' => 'Pax',
                    'value' => static function (Lead $model) {
                        return '<i class="fa fa-male"></i> <span title="adult">' . $model->adults . '</span> / <span title="child">' . $model->children . '</span>
        / <span title="infant">' . $model->infants . '</span>';
                    },
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'visible' => !($user->isAgent() || $user->isExAgent()),
                ],
                [
                    'header' => 'Quotes',
                    'value' => static function (Lead $model) {
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
                    ],
                    'visible' => !($user->isAgent() || $user->isExAgent()),
                ],
                [
                    'header' => 'Segments',
                    'value' => static function (Lead $model) {
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
                    ],
                    'visible' => !($user->isAgent() || $user->isExAgent()),
                ],
                [
                    'header' => 'Depart',
                    'value' => static function (Lead $model) {
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
                    ],
                    'visible' => !($user->isAgent() || $user->isExAgent()),
                ],
                [
                    'label' => 'Owner',
                    'attribute' => 'employee_id',
                    'format' => 'raw',
                    'value' => static function (Lead $model) {
                        return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
                    },
                ],
                [
                    'attribute' => 'created',
                    'value' => static function (Lead $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'l_last_action_dt',
                    'value' => static function (Lead $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->l_last_action_dt));
                    },
                    'format' => 'raw'
                ],

            ],
        ]) ?>

    </div>
</div>
