<?php

use common\components\grid\DateTimeColumn;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightQuoteBookingAirlineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Booking Airlines';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-booking-airline-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote Booking Airline', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-flight-quote-booking-airline']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'fqba_id',
            'fqba_fqb_id',
            'fqba_record_locator',
            'fqba_airline_code',
            'fqba_created_dt',
            ['class' => DateTimeColumn::class, 'attribute' => 'fqba_created_dt'],
            //'fqba_updated_dt',

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
