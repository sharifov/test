<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightQuotePaxPriceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-pax-price-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'qpp_id') ?>

    <?= $form->field($model, 'qpp_flight_quote_id') ?>

    <?= $form->field($model, 'qpp_flight_pax_code_id') ?>

    <?= $form->field($model, 'qpp_fare') ?>

    <?= $form->field($model, 'qpp_tax') ?>

    <?php // echo $form->field($model, 'qpp_system_mark_up') ?>

    <?php // echo $form->field($model, 'qpp_agent_mark_up') ?>

    <?php // echo $form->field($model, 'qpp_origin_fare') ?>

    <?php // echo $form->field($model, 'qpp_origin_currency') ?>

    <?php // echo $form->field($model, 'qpp_origin_tax') ?>

    <?php // echo $form->field($model, 'qpp_client_currency') ?>

    <?php // echo $form->field($model, 'qpp_client_fare') ?>

    <?php // echo $form->field($model, 'qpp_client_tax') ?>

    <?php // echo $form->field($model, 'qpp_created_dt') ?>

    <?php // echo $form->field($model, 'qpp_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
