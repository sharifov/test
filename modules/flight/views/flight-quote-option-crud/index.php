<?php

use common\components\grid\DateTimeColumn;
use modules\flight\src\entities\flightQuoteOption\FlightQuoteOption;
use yii\bootstrap4\Html;
use yii\grid\GridView;
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
                    return $flightQuoteOption->fqo_product_quote_option_id ? Html::a('<i class=""></i>') : null;
                }
            ],
            'fqo_flight_pax_id',
            'fqo_flight_quote_segment_id',
            'fqo_flight_quote_trip_id',
            'fqo_display_name',
            'fqo_markup_amount',
            'fqo_base_price',
            'fqo_total_price',
            'fqo_client_total',
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
