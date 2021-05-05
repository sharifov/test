<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteBookingAirline */

$this->title = 'Create Flight Quote Booking Airline';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Booking Airlines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-booking-airline-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
