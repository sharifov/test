<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;
use common\models\Quote;
use yii\helpers\Url;
use common\models\Airport;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $multipleForm \frontend\models\LeadMultipleForm */
/* @var $isAgent bool */
/* @var $salary float */
/* @var $salaryBy string */

$this->title = 'Sold Queue';

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}

$this->params['breadcrumbs'][] = $this->title;

?>

<h1><i class="fa fa-flag"></i> <?= \yii\helpers\Html::encode($this->title) ?></h1>

<style>
    .dropdown-menu {
        z-index: 1010 !important;
    }
</style>
<div class="lead-index">

    <?php Pjax::begin(); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>
    <?php if (isset($salary)): ?>
        <h3>Salary by <?= $salaryBy ?>: $<?= number_format($salary['salary'], 2) ?>
            (Base: $<?= round($salary['base']) ?>, Commission: <?= $salary['commission'] ?>%, Bonus:
            $<?= $salary['bonus'] ?>)</h3>
    <?php endif; ?>
    <?= $this->render('_search_sold', ['model' => $searchModel]); ?>

    <?php

    $gridColumns = [
        /*[
            'attribute' => 'pending',
            'label' => 'Pending Time',
            'value' => function ($model) {
                return Lead::getPendingAfterCreate($model->created);
            },
            'format' => 'raw'
        ],*/

        [
            'attribute' => 'id',
            'label' => 'Lead ID',
        ],
        [
            'attribute' => 'bo_flight_id',
            'label' => 'Sale ID (BO)',
            'value' => function (\common\models\Lead $model) {
                if (!empty($model['additional_information'])) {
                    $additionallyInfo = Lead::getLeadAdditionalInfo($model['additional_information']);
                    $ids = [];
                    foreach ($additionallyInfo as $additionally) {
                        /*if (!$additionally->tkt_processed) {
                            continue;
                        }*/
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
            'label' => 'PNR',
            'value' => function ($model) {
                if (!empty($model['additional_information'])) {
                    $additionallyInfo = Lead::getLeadAdditionalInfo($model['additional_information']);
                    $pnrs = [];
                    foreach ($additionallyInfo as $additionally) {
                        /*if (!$additionally->tkt_processed) {
                            continue;
                        }*/
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
                        /*if (!$additionally->tkt_processed) {
                            continue;
                        }*/
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
            // 'attribute' => 'client_id',
            'header' => 'Client',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) use ($isAgent) {

                if ($model->client) {
                    $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                    if ($clientName === 'Client Name') {
                        $clientName = '- - - ';
                    } else {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                    }
                } else {
                    $clientName = '-';
                }

                if ($isAgent && Yii::$app->user->id !== $model->employee_id) {
                    $emails = '- // - // - // -';
                    $phones = '- // - // - // -';
                } else {
                    $emails = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                    $phones = $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';
                }

                return $clientName . '<br/>' . $emails . '<br/>' . $phones;
            },
            'contentOptions' => [
                'style' => 'width: 260px;'
            ]
            // 'filter' => \common\models\Employee::getList()
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
                if($model->tips == 0) {
                    return '-';
                }
                $totalTipsTxt = "<strong>$" . number_format($model->tips, 2) . "</strong>";

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
                $mainAgentTipsTxt = "<strong>$" . number_format($model->tips * $mainAgentPercent / 100, 2) . "</strong>";

                return 'Tips: '.$totalTipsTxt.(($splitTipsTxt)?'<hr/>Split tips:<br/>'.$splitTipsTxt:'').'<hr/> '.
                    (($model->employee)?$model->employee->username:'Main agent').' tips: '.$mainAgentTipsTxt;
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Date of Issue',
            'attribute' => 'updated',
            'value' => function ($model) {
                return $model['updated'];
            },
            'format' => 'datetime',
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'updated',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy'
                ]
            ]),
            'contentOptions' => [
                'style' => 'width: 180px;text-align:center;'
            ]
        ],
        [
            'label' => 'Date of Departure',
            'value' => function ($model) {
                $quote = $model->getBookedQuote();
                if (!empty($quote) && isset($quote['reservation_dump']) && !empty($quote['reservation_dump'])) {
                    $data = [];
                    $segments = Quote::parseDump($quote['reservation_dump'], false, $data, true);
                    return $segments[0]['departureDateTime']->format('Y-m-d H:i');
                }
                $firstSegment = $model->getFirstFlightSegment();
                if (empty($firstSegment)) {
                    return '';
                }
                return $firstSegment['departure'];
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
            'value' => function ($model) {
                return Lead::getRating2($model['rating']);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'source_id',
            'label' => 'Market Info',
            'visible' => !$isAgent,
            'value' => function (\common\models\Lead $model) {
                return $model->source ? $model->source->name : '-';
            },
            'filter' => \common\models\Source::getList()
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-search"></i>', Url::to([
                        'lead/quote',
                        'type' => 'sold',
                        'id' => $model['id']
                    ]), [
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
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'toolbar' => false,
        'pjax' => false,
        'striped' => true,
        'condensed' => false,
        'responsive' => true,
        'hover' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => [
            'scrollingTop' => 20
        ],
        /*'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Sold</h3>'
        ]*/

    ]);

    ?>

    <?php Pjax::end(); ?>


</div>
