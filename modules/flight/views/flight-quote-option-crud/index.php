<?php

use common\components\grid\DateTimeColumn;
use modules\flight\src\entities\flightQuoteOption\FlightQuoteOption;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\flight\src\entities\flightQuoteOption\search\FlightQuoteOptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Options';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-option-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Flight Quote Option', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-flight-quote-option']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'fqo_id',
            [
                'attribute' => 'fqo_product_quote_option_id',
                'value' => static function (FlightQuoteOption $flightQuoteOption) {
                    $url = Url::toRoute(['/product/product-quote-option-crud/view', 'id' => $flightQuoteOption->fqo_product_quote_option_id]);
                    return $flightQuoteOption->fqo_product_quote_option_id ? Html::a('<i class="fa fa-link"></i> ' . $flightQuoteOption->fqo_product_quote_option_id, $url, [
                        'data-pjax' => 0,
                        'target' => '_blank'
                    ]) : null;
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'fqo_flight_pax_id',
                'value' => static function (FlightQuoteOption $flightQuoteOption) {
                    $url = Url::toRoute(['/flight/flight-pax/view', 'id' => $flightQuoteOption->fqo_flight_pax_id]);
                    return $flightQuoteOption->fqo_flight_pax_id ? Html::a('<i class="fa fa-link"></i> ' . $flightQuoteOption->fqo_flight_pax_id, $url, [
                        'data-pjax' => 0,
                        'target' => '_blank'
                    ]) : null;
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'fqo_flight_quote_segment_id',
                'value' => static function (FlightQuoteOption $flightQuoteOption) {
                    $url = Url::toRoute(['/flight/flight-quote-segment/view', 'id' => $flightQuoteOption->fqo_flight_quote_segment_id]);
                    return $flightQuoteOption->fqo_flight_quote_segment_id ? Html::a('<i class="fa fa-link"></i> ' . $flightQuoteOption->fqo_flight_quote_segment_id, $url, [
                        'data-pjax' => 0,
                        'target' => '_blank'
                    ]) : null;
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'fqo_flight_quote_trip_id',
                'value' => static function (FlightQuoteOption $flightQuoteOption) {
                    $url = Url::toRoute(['/flight/flight-quote-trip/view', 'id' => $flightQuoteOption->fqo_flight_quote_trip_id]);
                    return $flightQuoteOption->fqo_flight_quote_trip_id ? Html::a('<i class="fa fa-link"></i> ' . $flightQuoteOption->fqo_flight_quote_trip_id, $url, [
                        'data-pjax' => 0,
                        'target' => '_blank'
                    ]) : null;
                },
                'format' => 'raw'
            ],
            'fqo_display_name',
            'fqo_markup_amount',
            'fqo_usd_markup_amount',
            'fqo_base_price',
            'fqo_usd_base_price',
            'fqo_total_price',
            'fqo_usd_total_price',
            'fqo_currency',
            [
                'attribute' => 'fqo_created_dt',
                'class' => DateTimeColumn::class,
                'format' => 'byUserDateTime'
            ],
            [
                'attribute' => 'fqo_updated_dt',
                'class' => DateTimeColumn::class,
                'format' => 'byUserDateTime'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
