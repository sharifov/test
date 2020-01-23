<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightQuoteSegmentPaxBaggageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Segment Pax Baggages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-segment-pax-baggage-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote Segment Pax Baggage', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'qsb_id',
            'qsb_flight_pax_code_id',
            'qsb_flight_quote_segment_id',
            'qsb_airline_code',
            'qsb_allow_pieces',
            'qsb_allow_weight',
            'qsb_allow_unit',
            'qsb_allow_max_weight',
            'qsb_allow_max_size',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
