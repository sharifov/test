<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund */

$this->title = $model->fqtr_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Ticket Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-ticket-refund-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'fqtr_id' => $model->fqtr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'fqtr_id' => $model->fqtr_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'fqtr_id',
            'fqtr_ticket_number',
            'fqtr_created_dt',
            'fqtr_fqb_id',
        ],
    ]) ?>

</div>
