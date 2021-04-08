<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteTicket */

$this->title = 'Update Flight Quote Ticket: ' . $model->fqt_pax_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fqt_pax_id, 'url' => ['view', 'fqt_pax_id' => $model->fqt_pax_id, 'fqt_flight_id' => $model->fqt_flight_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-ticket-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
