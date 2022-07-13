<?php

use common\models\ProfitSplit;
use src\access\ListsAccess;
use src\auth\Auth;
use src\helpers\client\ClientReturnHelper;
use src\model\client\helpers\ClientFormatter;
use yii\helpers\Html;
use yii\widgets\Pjax;
use common\models\Lead;
use common\models\Airports;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $isAgent bool */
/* @var $salary float */
/* @var $salaryBy string */

$this->title = 'Sold Queue';

$lists = new ListsAccess(Yii::$app->user->id);

$this->params['breadcrumbs'][] = $this->title;

?>

<h1><i class="fa fa-flag"></i> <?= \yii\helpers\Html::encode($this->title) ?></h1>


<div class="lead-index">

    <?php Pjax::begin(['timeout' => 5000, 'clientOptions' => ['method' => 'GET'], 'scrollTo' => 0]); ?>

    <?= $this->render('_search_sold', ['model' => $searchModel]); ?>
<p>

</p>
    <?php

    $gridColumns = [
        /*[
            'attribute' => 'pending',
            'label' => 'Pending Time',
            'value' => static function ($model) {
                return Lead::getPendingAfterCreate($model->created);
            },
            'format' => 'raw'
        ],*/

        [
            'attribute' => 'id',
            'label' => 'Lead ID',
            'contentOptions' => [
                'style' => 'width:60px'
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
            'attribute' => 'bo_flight_id',
            'label' => 'Sale ID (BO)',
            'value' => static function (\common\models\Lead $model) {
                if (!empty($model['additional_information'])) {
                    $additionallyInfo = Lead::getLeadAdditionalInfo($model['additional_information']);
                    $ids = [];
                    foreach ($additionallyInfo as $additionally) {
                        $bo_sale_id = (!empty($additionally->bo_sale_id))
                            ? $additionally->bo_sale_id : $model->bo_flight_id;
                        $ids[] = $bo_sale_id;
                    }

                    $hrTag = Html::tag('hr', '', [
                        'style' => 'background-color: #a3b3bd;'
                    ]);
                    return implode($hrTag, $ids);
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
            'attribute' => 'source_id',
            'label' => 'Market Info',
            'visible' => !$isAgent,
            'value' => static function (\common\models\Lead $model) {
                return $model->source ? $model->source->name : '-';
            },
            'filter' => \common\models\Sources::getList(true)
        ],

        [
            'label' => 'PNR',
            'value' => static function ($model) {
                if (!empty($model['additional_information'])) {
                    $additionallyInfo = Lead::getLeadAdditionalInfo($model['additional_information']);
                    $pnrs = [];
                    foreach ($additionallyInfo as $additionally) {
                        $pnr = (!empty($additionally->pnr))
                            ? $additionally->pnr : '-';
                        $pnrs[] = $pnr;
                    }

                    $hrTag = Html::tag('hr', '', [
                        'style' => 'background-color: #a3b3bd;'
                    ]);
                    return implode($hrTag, $pnrs);
                }
                return '-';
            },
            'format' => 'raw',
            'contentOptions' => [
                'style' => 'width:80px'
            ],
            'options' => [
                'class' => 'width:80px'
            ],
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
                                $pax[] = strtoupper($passenger);
                            }
                            $content[] = implode('<br/>', $pax);
                        }
                    }
                }
                $hrTag = Html::tag('hr', '', [
                    'style' => 'background-color: #a3b3bd;'
                ]);
                return implode($hrTag, $content);
            },
            'format' => 'raw',
            'contentOptions' => [
                'style' => 'width: 250px;white-space: normal;',
            ]
        ],
//        [
//            // 'attribute' => 'client_id',
//            'header' => 'Client',
//            'format' => 'raw',
//            'value' => static function (\common\models\Lead $model) use ($isAgent) {
//                if ($model->client) {
//                    $clientName = $model->client->first_name . ' ' . $model->client->last_name;
//                    if ($clientName === 'Client Name') {
//                        $clientName = '- - - ';
//                    } else {
//                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
//                    }
//
//                    if ($model->client->isExcluded()) {
//                        $clientName = ClientFormatter::formatExclude($model->client)  . $clientName;
//                    }
//                } else {
//                    $clientName = '-';
//                }
//
//                /*if ($isAgent && Yii::$app->user->id !== $model->employee_id) {
//                    $emails = '- // - // - // -';
//                    $phones = '- // - // - // -';
//                } else {
//                    $emails = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
//                    $phones = $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';
//                }*/
//
//                return $clientName /* . '<br/>' . $emails . '<br/>' . $phones*/;
//            },
//            'contentOptions' => [
//                'style' => 'width: 200px;'
//            ]
//            // 'filter' => \common\models\Employee::getList()
//        ],
        [
            'label' => 'Destination',
            'value' => static function (\common\models\Lead $model) {
                $segments = $model->leadFlightSegments;
                $segmentData = [];
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
            'label' => 'Client Return Type',
            'value' => static function (Lead $model) {
                return ClientReturnHelper::displayClientReturnLabels($model->client_id, Auth::id());
            },
            'format' => 'raw'
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
                        //$model->totalProfit = $quote->getEstimationProfit();
                        $model->totalProfit = 0;
                        $totalProfitTxt = "<strong>$" . number_format($model->totalProfit, 2) . "</strong>";
                    }
                }

                $splitProfitTxt = '';
                $splitProfit = $model->getAllProfitSplits();
                $return = [];
                /** @var ProfitSplit $split */
                foreach ($splitProfit as $key => $split) {
                    if ($model->employee_id && $split->ps_user_id === $model->employee_id) {
                        unset($splitProfit[$key]);
                        continue;
                    }

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
            'format' => 'raw',
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

//                $mainAgentPercent = 100;
//                if ($model->splitTipsPercentSum > 0) {
//                    $mainAgentPercent -= $model->splitTipsPercentSum;
//                }
//                $mainAgentTipsTxt = "<strong>$" . number_format($model->getTotalTips() * $mainAgentPercent / 100, 2) . "</strong>";

                return 'Tips: ' . $totalTipsTxt . (($splitTipsTxt) ? '<hr/>Split tips:<br/>' . $splitTipsTxt : '');
            },
            'format' => 'raw',
        ],
        [
            'class' => \common\components\grid\DateTimeColumn::class,
            'label' => 'Sold Date',
            'attribute' => 'l_status_dt',
        ],

        /*[
            'label' => 'Sold Date',
            'attribute' => 'last_ticket_date',
            'value' => static function (Lead $model) {
                return ($model->leadFlowSold && $model->leadFlowSold->created) ? Yii::$app->formatter->asDatetime(strtotime($model->leadFlowSold->created)) : '';
            },
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'last_ticket_date',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ],
                'options' => [
                    'autocomplete' => 'off'
                ]
            ]),
            'contentOptions' => [
                'style' => 'width: 100px;text-align:center;'
            ]
        ],*/
        [
            'label' => 'Date of Departure',
            'value' => static function ($model) {
                if ($date = $model->getDeparture()) {
                    return  date('Y-m-d', strtotime($date));
                }
                return '';
            },
            'format' => 'raw',
            'contentOptions' => [
                'style' => 'width:100px'
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
            'value' => static function ($model) {
                return Lead::getRating2($model['rating']);
            },
            'format' => 'raw'
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, Lead $model, $key) {
                    return Html::a('<i class="fa fa-search"></i> view', [
                        'lead/view',
                        'gid' => $model->gid
                    ], [
                        'class' => 'btn btn-info btn-xs',
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => 'View lead'
                    ]);
                }
            ]
        ]
    ];

    ?>
    <?php
    echo \yii\grid\GridView::widget([
        'id' => 'lead-sold-gv',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,

        /*'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Sold</h3>'
        ]*/

    ]);
//  echo GridView::widget([
//        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
//        'columns' => $gridColumns,
//        'toolbar' => false,
//        'pjax' => false,
//        'striped' => true,
//        'condensed' => false,
//        'responsive' => true,
//        'hover' => true,
//        'floatHeader' => true,
//        'floatHeaderOptions' => [
//            'scrollingTop' => 20
//        ],
//        /*'panel' => [
//            'type' => GridView::TYPE_PRIMARY,
//            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Sold</h3>'
//        ]*/
//
//    ]);

    ?>

    <?php Pjax::end(); ?>

</div>
