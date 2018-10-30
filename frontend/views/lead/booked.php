<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;
use yii\helpers\Url;
use common\models\Airport;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $isAgent bool */

$this->title = 'Booked Queue';

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
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
<h1>
	<?=\yii\helpers\Html::encode($this->title)?>
</h1>
<div class="lead-index">

    <?php Pjax::begin(); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>

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
            'attribute' => 'bo_flight_id',
            'label' => 'Sale ID (BO)',
            'options' => [
                'style' => 'width:80px'
            ]
        ],
        [
            'attribute' => 'pending',
            'label' => 'Pending Time',
            'value' => function (\common\models\Lead $model) {
                return Yii::$app->formatter->asRelativeTime(strtotime($model->created)); // Lead::getPendingAfterCreate($model->created);
            },
            'format' => 'raw'
        ],
        [
            'label' => 'PNR',
            'value' => function ($model) {
                if (! empty($model['additional_information'])) {
                    $additionally = new \common\models\local\LeadAdditionalInformation();
                    $additionally->setAttributes(@json_decode($model['additional_information'], true));
                    return (! empty($additionally->pnr)) ? $additionally->pnr : '-';
                }
                return '-';
            }
        ],
        [
            'label' => 'Passengers',
            'value' => function ($model) {
                $content = [];
                if (! empty($model['additional_information'])) {
                    $additionally = new \common\models\local\LeadAdditionalInformation();
                    $additionally->setAttributes(@json_decode($model['additional_information'], true));
                    $content = (! empty($additionally->passengers)) ? $additionally->passengers : $content;
                }
                return implode('<br/>', $content);
            },
            'format' => 'raw',
            'contentOptions' => [
                'style' => 'width: 200px;'
            ]
        ],
        [
            'label' => 'Destination',
            'value' => function (\common\models\Lead $model) {
                $segments = $model->leadFlightSegments;
                $segmentData = [];
                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        $airport = Airport::findIdentity($segment->destination);
                        if ($airport) {
                            return $airport->city . " (" . $segment->destination . ")";
                        }
                        return $segment->destination;
                    }
                }
                return '';
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
            'label' => 'Total Profit',
            'value' => function ($model) {
                $quote = $model->getBookedQuote();
                if (empty($quote)) {
                    return '';
                }
                $model->totalProfit = $quote->getTotalProfit();
                return "<strong>$" . number_format($model->totalProfit, 2) . "</strong>";
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Split Profit',
            'value' => function ($model) {
                $splitProfit = $model->getAllProfitSplits();
                $return = [];
                foreach ($splitProfit as $split) {
                    $model->splitProfitPercentSum += $split->ps_percent;
                    $return[] = '<b>' . $split->psUser->username . '</b> (' . $split->ps_percent . '%) $' . number_format($split->countProfit($model->totalProfit), 2);
                }
                if (empty($return)) {
                    return '-';
                }
                return implode('<br/>', $return);
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Main Agent Profit',
            'value' => function ($model) {
                $mainAgentPercent = 100;
                if ($model->splitProfitPercentSum > 0) {
                    $mainAgentPercent -= $model->splitProfitPercentSum;
                }
                return "<strong>$" . number_format($model->totalProfit * $mainAgentPercent / 100, 2) . "</strong>";
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'update',
            'label' => 'Last Update',
            'value' => function (\common\models\Lead $model) {
                return '<span title="' . Yii::$app->formatter->asDatetime(strtotime($model->updated)) . '">' . Yii::$app->formatter->asRelativeTime(strtotime($model->updated)) . '</span>';
            },
            'format' => 'raw'
        ],
        [
            'label' => 'VTF',
            'value' => function (\common\models\Lead $model) {
                $additionally = new \common\models\local\LeadAdditionalInformation();
                if (! empty($model['additional_information'])) {
                    $additionally->setAttributes(@json_decode($model['additional_information'], true));
                }
                $labelVTF = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (! empty($additionally->vtf_processed)) {
                    $labelVTF = '<span class="label label-success"><i class="fa fa-check"></i></span>';
                }
                return $labelVTF;
            },
            'format' => 'raw'
        ],
        [
            'label' => 'TKT',
            'value' => function (\common\models\Lead $model) {
                $additionally = new \common\models\local\LeadAdditionalInformation();
                if (! empty($model['additional_information'])) {
                    $additionally->setAttributes(@json_decode($model['additional_information'], true));
                }
                $labelTKT = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (! empty($additionally->tkt_processed)) {
                    $labelTKT = '<span class="label label-success"><i class="fa fa-check"></i></span>';
                }
                return $labelTKT;
            },
            'format' => 'raw'
        ],
        [
            'label' => 'EXP',
            'value' => function (\common\models\Lead $model) {
                $additionally = new \common\models\local\LeadAdditionalInformation();
                if (! empty($model['additional_information'])) {
                    $additionally->setAttributes(@json_decode($model['additional_information'], true));
                }
                $labelEXP = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (! empty($additionally->exp_processed)) {
                    $labelEXP = '<span class="label label-success"><i class="fa fa-check"></i></span>';
                }
                return $labelEXP;
            },
            'format' => 'raw'
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
            'filter' => $projectList,
            'visible' => ! $isAgent
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, \common\models\Lead $model, $key) {

                    $buttons = '';

                    $buttons .= Html::a('<i class="fa fa-search"></i>', [
                        'lead/quote',
                        'type' => 'processing',
                        'id' => $model->id
                    ], [
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