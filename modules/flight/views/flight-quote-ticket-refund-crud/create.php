<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund */

$this->title = 'Create Flight Quote Ticket Refund';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Ticket Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-ticket-refund-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
