<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
use common\models\Quote;
use yii\helpers\Url;
/* @var $this yii\web\View
 * @var $searchModel common\models\search\ApiLogSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $historyParams []
 * @var $kpiHistory common\models\KpiHistory
 * @var $month string
 * @var $agent string
 * */

$this->title = 'Salary by '.$month.' for '.$agent.': $'.$kpiHistory->getSalary();

$this->params['breadcrumbs'][] = ['label' => 'KPI', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <?= $this->render('_form', [
        'model' => $kpiHistory,
        'isAgent' => $isAgent,
    ]) ?>

    <?php $gridColumns = [
        [
            'attribute' => 'id',
            'label' => 'Lead ID',
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
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, $model, $key) {
                return Html::a('<i class="fa fa-search"></i>', ['lead/view', 'id' => $model->id], [
                    'class' => 'btn btn-info btn-xs',
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'title' => 'View lead'
                ]);
            }
            ]
            ]
    ];?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => $gridColumns,
    ]); ?>
    <?php Pjax::end(); ?>
</div>
