<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;
use common\models\QuoteCommunicationOpenLog;

/* @var $this yii\web\View */
/* @var $searchModel QuoteCommunicationOpenLog */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quote Communication Open Log';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-communication-open-log-index">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <?= Html::tag('p', Html::a('Create Quote Communication Open Log', ['create'], ['class' => 'btn btn-success'])) ?>

    <?php Pjax::begin(); ?>

    <?php
    try {
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'qcol_id',
                    'options' => [
                        'width' => '100px'
                    ]
                ],
                [
                    'attribute' => 'qcol_quote_communication_id'
                ],
                [
                    'class' => DateTimeColumn::class,
                    'attribute' => 'qcol_created_dt',
                    'format' => 'byUserDateTime',
                    'options' => [
                        'width' => '200px'
                    ],
                ],
                [
                    'class' => ActionColumn::class,
                    'urlCreator' => static function ($action, QuoteCommunicationOpenLog $model, $key, $index, $column): string {
                        return Url::toRoute([$action, 'qcol_id' => $model->qcol_id]);
                    }
                ],
            ],
        ]);
    } catch (\Exception $e) {
        echo Html::tag('pre', Html::encode($e->getMessage()));
    }
    ?>

    <?php Pjax::end(); ?>

</div>
