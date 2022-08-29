<?php

use kartik\grid\GridViewInterface;
use src\model\user\reports\stats\UserStatsReport;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/** @var ActiveDataProvider $dataProvider */
/** @var UserStatsReport $searchModel */
/** @var string $type */

$isConversionHidden = $type === 'sold';

Pjax::begin([
    'id' => 'user-leads-list-modal-' . $type,
    'clientOptions' => ['method' => 'POST'],
    'linkSelector' => 'a:not(.linksWithTarget)',
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'responsive' => true,
    'hover' => true,
    'panel' => [
        'type' => GridViewInterface::TYPE_PRIMARY,
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Leads by user</h3>',
    ],
    'columns' => [
        [
            'attribute' => 'id',
            'format' => 'raw',
            'value' => fn (array $model): string => Html::a(
                $model['id'],
                ['leads/view', 'id' => $model['id']],
                ['target' => '_blank', 'class' => 'linksWithTarget']
            ),
        ],
        [
            'attribute' => 'conversion_percent',
            'visible' => $isConversionHidden,
        ],
        [
            'attribute' => 'split_share',
            'visible' => $isConversionHidden,
        ],
        [
            'attribute' => 'qualified_leads_taken',
            'visible' => $isConversionHidden,
        ],
        [
            'attribute' => 'gross_profit',
            'visible' => $isConversionHidden,
            'value' => fn (array $model): string => Yii::$app->formatter->asNumCurrency($model['gross_profit']),
        ],
        [
            'attribute' => 'tips',
            'visible' => $isConversionHidden,
            'value' => fn (array $model): string => Yii::$app->formatter->asNumCurrency($model['tips']),
        ],
        'sales_conversion_call_priority',
        'call_priority_current',
        'gross_profit_call_priority',
        [
            'attribute' => 'created',
            'label' => 'Date'
        ],
    ]
]);

Pjax::end();
