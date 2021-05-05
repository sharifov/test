<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteBookingAirline */

$this->title = 'Update Flight Quote Booking Airline: ' . $model->fqba_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Booking Airlines', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fqba_id, 'url' => ['view', 'id' => $model->fqba_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-booking-airline-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
