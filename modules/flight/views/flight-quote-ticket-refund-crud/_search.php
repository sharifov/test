<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteTicketRefund\search\FlightQuoteTicketRefundSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-ticket-refund-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fqtr_id') ?>

    <?= $form->field($model, 'fqtr_ticket_number') ?>

    <?= $form->field($model, 'fqtr_created_dt') ?>

    <?= $form->field($model, 'fqtr_fqb_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
