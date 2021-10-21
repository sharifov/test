<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund */

$this->title = 'Update Flight Quote Ticket Refund: ' . $model->fqtr_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Ticket Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fqtr_id, 'url' => ['view', 'fqtr_id' => $model->fqtr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-ticket-refund-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
