<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightQuoteTicketSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="flight-quote-ticket-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fqt_pax_id') ?>

    <?= $form->field($model, 'fqt_ticket_number') ?>

    <?= $form->field($model, 'fqt_created_dt') ?>

    <?= $form->field($model, 'fqt_updated_dt') ?>

    <?= $form->field($model, 'fqt_fqb_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
