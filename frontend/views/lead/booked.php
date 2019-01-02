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
    <i class="fa fa-flag-o"></i>
    <?= \yii\helpers\Html::encode($this->title) ?>
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
            'value' => function (\common\models\Lead $model) {
                if (!empty($model['additional_information'])) {
                    $additionallyInfo = Lead::getLeadAdditionalInfo($model['additional_information']);
                    $ids = [];
                    foreach ($additionallyInfo as $additionally) {
                        $newRows = '';
                        if (!empty($additionally->passengers)) {
                            for ($i = 0; $i < count($additionally->passengers); $i++) {
                                $newRows .= '<br/>';
                            }
                        }
                        $bo_sale_id = (!empty($additionally->bo_sale_id))
                            ? $additionally->bo_sale_id : $model->bo_flight_id;
                        $ids[] = $bo_sale_id . $newRows;
                    }

                    $divTag = Html::tag('div', '', [
                        'style' => 'border: 1px solid #a3b3bd; margin: 0px 0 5px;'
                    ]);
                    return implode($divTag, $ids);
                }
                return 0;
            },
            'format' => 'raw',
            'contentOptions' => [
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
                if (!empty($model['additional_information'])) {
                    $additionallyInfo = Lead::getLeadAdditionalInfo($model['additional_information']);
                    $pnrs = [];
                    foreach ($additionallyInfo as $additionally) {
                        $newRows = '';
                        if (!empty($additionally->passengers)) {
                            for ($i = 0; $i < count($additionally->passengers); $i++) {
                                $newRows .= '<br/>';
                            }
                        }
                        $pnr = (!empty($additionally->pnr))
                            ? $additionally->pnr : '-';
                        $pnrs[] = $pnr . $newRows;
                    }

                    $divTag = Html::tag('div', '', [
                        'style' => 'border: 1px solid #a3b3bd; margin: 0px 0 5px;'
                    ]);
                    return implode($divTag, $pnrs);
                }
                return '-';
            },
            'format' => 'raw',
        ],
        [
            'label' => 'Passengers',
            'value' => function ($model) {
                $content = [];
                if (!empty($model['additional_information'])) {
                    $additionallyInfo = Lead::getLeadAdditionalInfo($model['additional_information']);
                    foreach ($additionallyInfo as $additionally) {
                        if (!empty($additionally->passengers)) {
                            $pax = [];
                            foreach ($additionally->passengers as $passenger) {
                                $pax[] = strtoupper($passenger);
                            }
                            $content[] = implode('<br/>', $pax);
                        }
                    }
                }
                $divTag = Html::tag('div', '', [
                    'style' => 'border: 1px solid #a3b3bd; margin: 0px 0 5px;'
                ]);
                return implode($divTag, $content);
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
            'visible' => !$isAgent
            ],
            [
                'label' => 'Profit',
                'value' => function (\common\models\Lead $model) {
                $totalProfitTxt = '';
                if ($model->final_profit !== null) {
                    $totalProfitTxt = "<strong>$" . number_format($model->final_profit, 2) . "</strong>";
                }else{
                    $quote = $model->getBookedQuote();
                    if (empty($quote)) {
                        $totalProfitTxt = "<strong>$" . number_format(0, 2) . "</strong>";
                    }else{
                        $model->totalProfit = $quote->getEstimationProfit();
                        $totalProfitTxt = "<strong>$" . number_format($model->totalProfit, 2) . "</strong>";
                    }
                }

                $splitProfitTxt = '';
                $splitProfit = $model->getAllProfitSplits();
                $return = [];
                foreach ($splitProfit as $split) {
                    $model->splitProfitPercentSum += $split->ps_percent;
                    $return[] = '<b>' . $split->psUser->username . '</b> (' . $split->ps_percent . '%) $' . number_format($split->countProfit($model->totalProfit), 2);
                }
                if (!empty($return)) {
                    $splitProfitTxt = implode('<br/>', $return);
                }

                $mainAgentPercent = 100;
                if ($model->splitProfitPercentSum > 0) {
                    $mainAgentPercent -= $model->splitProfitPercentSum;
                }
                $mainAgentProfitTxt = "<strong>$" . number_format($model->totalProfit * $mainAgentPercent / 100, 2) . "</strong>";

                return 'Total profit: '.$totalProfitTxt.(($splitProfitTxt)?'<hr/>Split profit:<br/>'.$splitProfitTxt:'').
                '<hr/> '.(($model->employee)?$model->employee->username:'Main agent').' profit: '.$mainAgentProfitTxt;

            },
            'format' => 'raw'
                    ],
                    [
                        'label' => 'Tips',
                        'value' => function (\common\models\Lead $model) {
                        if($model->totalTips == 0) {
                            return '-';
                        }
                        $totalTipsTxt = "<strong>$" . number_format($model->totalTips, 2) . "</strong>";

                        $splitTipsTxt = '';
                        $splitTips = $model->getAllTipsSplits();
                        $return = [];
                        foreach ($splitTips as $split) {
                            $model->splitTipsPercentSum += $split->ts_percent;
                            $return[] = '<b>' . $split->tsUser->username . '</b> (' . $split->ts_percent . '%) $' . number_format($split->countTips($model->tips), 2);
                        }
                        if (!empty($return)) {
                            $splitTipsTxt = implode('<br/>', $return);
                        }

                        $mainAgentPercent = 100;
                        if ($model->splitTipsPercentSum > 0) {
                            $mainAgentPercent -= $model->splitTipsPercentSum;
                        }
                        $mainAgentTipsTxt = "<strong>$" . number_format($model->totalTips * $mainAgentPercent / 100, 2) . "</strong>";

                        return 'Tips: '.$totalTipsTxt.(($splitTipsTxt)?'<hr/>Split tips:<br/>'.$splitTipsTxt:'').'<hr/> '.
                            (($model->employee)?$model->employee->username:'Main agent').' tips: '.$mainAgentTipsTxt;
                    },
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
                $labelVTF = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (!empty($model['additional_information'])) {
                    $additionallyInfo = Lead::getLeadAdditionalInfo($model['additional_information']);
                    $labels = [];
                    foreach ($additionallyInfo as $additionally) {
                        $newRows = '';
                        if (!empty($additionally->passengers)) {
                            for ($i = 0; $i < count($additionally->passengers); $i++) {
                                $newRows .= '<br/>';
                            }
                        }
                        $label = (!empty($additionally->vtf_processed))
                            ? '<span class="label label-success"><i class="fa fa-check"></i></span>'
                            : $labelVTF;

                        $labels[] = $label . $newRows;
                    }

                    $divTag = Html::tag('div', '', [
                        'style' => 'border: 1px solid #a3b3bd; margin: 0px 0 5px;'
                    ]);

                    return implode($divTag, $labels);
                }

                return $labelVTF;

            },
            'format' => 'raw'
        ],
        [
            'label' => 'TKT',
            'value' => function (\common\models\Lead $model) {
                $labelTKT = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (!empty($model['additional_information'])) {
                    $additionallyInfo = Lead::getLeadAdditionalInfo($model['additional_information']);
                    $labels = [];
                    foreach ($additionallyInfo as $additionally) {
                        $newRows = '';
                        if (!empty($additionally->passengers)) {
                            for ($i = 0; $i < count($additionally->passengers); $i++) {
                                $newRows .= '<br/>';
                            }
                        }
                        $label = (!empty($additionally->tkt_processed))
                            ? '<span class="label label-success"><i class="fa fa-check"></i></span>'
                            : $labelTKT;

                        $labels[] = $label . $newRows;
                    }

                    $divTag = Html::tag('div', '', [
                        'style' => 'border: 1px solid #a3b3bd; margin: 0px 0 5px;'
                    ]);

                    return implode($divTag, $labels);
                }

                return $labelTKT;
            },
            'format' => 'raw'
        ],
        [
            'label' => 'EXP',
            'value' => function (\common\models\Lead $model) {
                $labelEXP = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                if (!empty($model['additional_information'])) {
                    $additionallyInfo = Lead::getLeadAdditionalInfo($model['additional_information']);
                    $labels = [];
                    foreach ($additionallyInfo as $additionally) {
                        $newRows = '';
                        if (!empty($additionally->passengers)) {
                            for ($i = 0; $i < count($additionally->passengers); $i++) {
                                $newRows .= '<br/>';
                            }
                        }
                        $label = (!empty($additionally->exp_processed))
                            ? '<span class="label label-success"><i class="fa fa-check"></i></span>'
                            : $labelEXP;

                        $labels[] = $label . $newRows;
                    }

                    $divTag = Html::tag('div', '', [
                        'style' => 'border: 1px solid #a3b3bd; margin: 0px 0 5px;'
                    ]);

                    return implode($divTag, $labels);
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
            'visible' => !$isAgent
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, \common\models\Lead $model, $key) {

                    $buttons = '';

                    $buttons .= Html::a('<i class="fa fa-search"></i>', [
                        'lead/view',
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