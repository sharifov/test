<?php

use common\components\grid\DateTimeColumn;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var modules\flight\models\search\FlightQuoteFlightSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Flight Quote Flights';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-flight-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create Flight Quote Flight', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-flight-quote-flight']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'fqf_id',
            'fqf_fq_id',
            'fqf_record_locator',
            'fqf_gds',
            'fqf_gds_pcc',
            'fqf_type_id',
            'fqf_cabin_class',
            'fqf_trip_type_id',
            'fqf_main_airline',
            'fqf_fare_type_id',
            'fqf_status_id',
            'fqf_booking_id',
            'fqf_pnr',
            'fqf_validating_carrier',

            ['class' => DateTimeColumn::class, 'attribute' => 'fqf_created_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>