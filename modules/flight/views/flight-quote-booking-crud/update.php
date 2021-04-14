<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteBooking */

$this->title = 'Update Flight Quote Booking: ' . $model->fqb_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Bookings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fqb_id, 'url' => ['view', 'id' => $model->fqb_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-booking-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
