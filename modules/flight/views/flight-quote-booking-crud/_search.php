<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightQuoteBookingSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="flight-quote-booking-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fqb_id') ?>

    <?= $form->field($model, 'fqb_fqf_id') ?>

    <?= $form->field($model, 'fqb_booking_id') ?>

    <?= $form->field($model, 'fqb_pnr') ?>

    <?= $form->field($model, 'fqb_gds') ?>

    <?php // echo $form->field($model, 'fqb_gds_pcc') ?>

    <?php // echo $form->field($model, 'fqb_validating_carrier') ?>

    <?php // echo $form->field($model, 'fqb_created_dt') ?>

    <?php // echo $form->field($model, 'fqb_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
