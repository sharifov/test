<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteBooking */

$this->title = 'Create Flight Quote Booking';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Bookings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-booking-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
