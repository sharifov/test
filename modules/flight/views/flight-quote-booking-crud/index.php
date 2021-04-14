<?php

use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightQuoteBookingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Bookings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-booking-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote Booking', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-flight-quote-booking']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'fqb_id',
            'fqb_fqf_id',
            'fqb_booking_id',
            'fqb_pnr',
            'fqb_gds',
            //'fqb_gds_pcc',
            'fqb_validating_carrier',
            ['class' => DateTimeColumn::class, 'attribute' => 'fqb_created_dt'],
            //'fqb_updated_dt',

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
