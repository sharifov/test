<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuotePaxPrice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-pax-price-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'qpp_flight_quote_id')->textInput() ?>

    <?= $form->field($model, 'qpp_flight_pax_code_id')->textInput() ?>

    <?= $form->field($model, 'qpp_fare')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qpp_tax')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qpp_system_mark_up')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qpp_agent_mark_up')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qpp_origin_fare')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qpp_origin_currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qpp_origin_tax')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qpp_client_currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qpp_client_fare')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qpp_client_tax')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qpp_created_dt')->textInput() ?>

    <?= $form->field($model, 'qpp_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
