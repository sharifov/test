<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightQuoteSegmentPaxBaggageChargeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Segment Pax Baggage Charges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-segment-pax-baggage-charge-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote Segment Pax Baggage Charge', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'qsbc_id',
            'qsbc_flight_pax_code_id',
            'qsbc_flight_quote_segment_id',
            'qsbc_first_piece',
            'qsbc_last_piece',
            'qsbc_origin_price',
            'qsbc_origin_currency',
            'qsbc_price',
            'qsbc_client_price',
            'qsbc_client_currency',
            'qsbc_max_weight',
            'qsbc_max_size',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
