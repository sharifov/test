<?php

use common\models\QuoteSegmentBaggageCharge;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\QuoteSegmentBaggageChargeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quote Segment Baggage Charges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-segment-baggage-charge-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'qsbc_pax_code',
            'qsbc_segment_id',
            'qsbc_first_piece',
            'qsbc_last_piece',
            'qsbc_price',
            'qsbc_currency',
            'qsbc_max_weight',
            'qsbc_max_size',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, QuoteSegmentBaggageCharge $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'qsbc_id' => $model->qsbc_id]);
                }
            ],
        ],
    ]); ?>


</div>
