<?php

use dosamigos\datepicker\DatePicker;
use src\access\EmployeeProjectAccess;
use src\access\ListsAccess;
use yii\helpers\Html;
use yii\widgets\Pjax;
use common\models\Lead;
use common\models\Airports;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $isAgent bool */

$this->title = 'Booked Queue';

$lists = new ListsAccess(Yii::$app->user->id);

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

    <?php Pjax::begin(['scrollTo' => 0]); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>

    <?php

    $gridColumns = [
        [
            'attribute' => 'id',
            'label' => 'Lead ID',
//            'value' => static function (\common\models\Lead $model) {
//                return $model->id;
//            },
            'options' => [
                'style' => 'width:80px'
            ]
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
            'attribute' => 'uid',
//            'value' => static function (\common\models\Lead $model) {
//                return $model->uid;
//            },
            'options' => [
                'style' => 'width:120px'
            ]
        ],

        [
            'attribute' => 'bo_flight_id',
            'label' => 'BO ID',
            'value' => static function (\common\models\Lead $model) {
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
            'class' => \common\components\grid\project\ProjectColumn::class,
            'attribute' => 'project_id',
            'relation' => 'project'
        ],

        [
            'attribute' => 'pending',
            'label' => 'Pending Time',
            'value' => static function (\common\models\Lead $model) {
                $date = $model->getStatusDate(Lead::STATUS_BOOKED);
                if (!$date) {
                    $date = $model->updated;
                }

                $dateTS = strtotime($date);

                $diffTime = time() - $dateTS;
                $diffHours = (int) ($diffTime / (60 * 60));

                return ($diffHours > 3 && $diffHours < 73 ) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($dateTS);
            },
            'format' => 'raw'
        ],

        [
            'label' => 'PNR',
            'value' => static function ($model) {
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
            'value' => static function ($model) {
                $content = [];
                if (!empty($model['additional_information'])) {
                    $additionallyInfo = Lead::getLeadAdditionalInfo($model['additional_information']);
                    foreach ($additionallyInfo as $additionally) {
                        if (!empty($additionally->passengers)) {
                            $pax = [];
                            foreach ($additionally->passengers as $passenger) {
                                $pax[] = '<i class="fa fa-user-o success"></i> ' . strtoupper($passenger);
                            }
                            $content[] = implode('<br/>', $pax);
                        }
                    }
                }
                $divTag = Html::tag('div', '', [
                    'style' => 'border: 1px solid #a3b3bd; margin: 0px 0 5px;'
                ]);
                return '<small>' . implode($divTag, $content) . '</small>';
            },
            'format' => 'raw',
            'contentOptions' => [
                'style' => 'width: 300px;'
            ]
        ],
        [
            'label' => 'Destination',
            'value' => static function (\common\models\Lead $model) {
                $segments = $model->leadFlightSegments;
                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        $airport = Airports::findByIata($segment->destination);
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
            'value' => static function (\common\models\Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
            },
            'filter' => $lists->getEmployees(),
            'visible' => $lists->getEmployees()
            ],
            [
                'label' => 'Profit',
                'value' => static function (\common\models\Lead $model) {
                    $totalProfitTxt = '';
                    if ($model->getFinalProfit()) {
                        $model->totalProfit = $model->getFinalProfit();
                        $totalProfitTxt = "<strong>$" . number_format($model->getFinalProfit(), 2) . "</strong>";
                    } else {
                        $quote = $model->getBookedQuote();
                        if (empty($quote)) {
                            $totalProfitTxt = "<strong>$" . number_format(0, 2) . "</strong>";
                        } else {
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

                    return 'Total profit: ' . $totalProfitTxt . (($splitProfitTxt) ? '<hr/>Split profit:<br/>' . $splitProfitTxt : '') .
                    '<hr/> ' . (($model->employee) ? $model->employee->username : 'Main agent') . ' profit: ' . $mainAgentProfitTxt;
                },
            'format' => 'raw'
                    ],
                    [
                        'label' => 'Tips',
                        'value' => static function (\common\models\Lead $model) {
                            if ($model->getTotalTips() == 0) {
                                return '-';
                            }
                            $totalTipsTxt = "<strong>$" . number_format($model->getTotalTips(), 2) . "</strong>";

                            $splitTipsTxt = '';
                            $splitTips = $model->getAllTipsSplits();
                            $return = [];
                            foreach ($splitTips as $split) {
                                $model->splitTipsPercentSum += $split->ts_percent;
                                $return[] = '<b>' . $split->tsUser->username . '</b> (' . $split->ts_percent . '%) $' . number_format($split->countTips($model->getTotalTips()), 2);
                            }
                            if (!empty($return)) {
                                $splitTipsTxt = implode('<br/>', $return);
                            }

//                            $mainAgentPercent = 100;
//                            if ($model->splitTipsPercentSum > 0) {
//                                $mainAgentPercent -= $model->splitTipsPercentSum;
//                            }
//                            $mainAgentTipsTxt = "<strong>$" . number_format($model->getTotalTips() * $mainAgentPercent / 100, 2) . "</strong>";

                            return 'Tips: ' . $totalTipsTxt . (($splitTipsTxt) ? '<hr/>Split tips:<br/>' . $splitTipsTxt : '');
                        },
                    'format' => 'raw'
        ],
        [
            'attribute' => 'update',
            'label' => 'Last Update',
            'value' => static function (\common\models\Lead $model) {
                return '<span title="' . Yii::$app->formatter->asDatetime(strtotime($model->updated)) . '">' . Yii::$app->formatter->asRelativeTime(strtotime($model->updated)) . '</span>';
            },
            'format' => 'raw'
        ],
        [
            'label' => 'VTF',
            'value' => static function (\common\models\Lead $model) {
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
            'value' => static function (\common\models\Lead $model) {
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
            'value' => static function (\common\models\Lead $model) {
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
            'value' => static function (\common\models\Lead $model) {
                return Lead::getRating2($model->rating);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'created',
            //'label' => 'Pending Time',
            'value' => static function (\common\models\Lead $model) {

                $createdTS = strtotime($model->created);

                $diffTime = time() - $createdTS;
                $diffHours = (int) ($diffTime / (60 * 60));

                $str = ($diffHours > 3 && $diffHours < 73 ) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));

                return $str;
            },
            'options' => [
                'style' => 'width:160px'
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
                    'clearBtn' => true
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'Choose Date'
                ],
                'clientEvents' => [
                    'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                ]
            ]),
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, \common\models\Lead $model, $key) {

                    $buttons = '';

                    $buttons .= Html::a('<i class="fa fa-search"></i> View', [
                        'lead/view',
                        'gid' => $model->gid
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
        'id' => 'lead-booked-gv',
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
