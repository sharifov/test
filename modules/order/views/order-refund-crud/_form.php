<?php

use common\models\Currency;
use modules\order\src\entities\orderRefund\OrderRefundClientStatus;
use modules\order\src\entities\orderRefund\OrderRefundStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderRefund\OrderRefund */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-refund-form">
  <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-2">

      <?= $form->errorSummary($model) ?>

      <?= $form->field($model, 'orr_order_id')->input('number') ?>

      <?= $form->field($model, 'orr_selling_price')->input('number', [
          'step' => 'any',
      ]) ?>

      <?= $form->field($model, 'orr_penalty_amount')->input('number', [
          'step' => 'any',
      ]) ?>

      <?= $form->field($model, 'orr_processing_fee_amount')->input('number', [
          'step' => 'any',
      ]) ?>

      <?= $form->field($model, 'orr_charge_amount')->input('number', [
          'step' => 'any',
      ]) ?>

      <?= $form->field($model, 'orr_refund_amount')->input('number', [
          'step' => 'any',
      ]) ?>

      <?= $form->field($model, 'orr_client_status_id')->dropDownList(OrderRefundClientStatus::getList(), [
          'prompt' => '---'
      ]) ?>

      <div class="form-group">
          <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
      </div>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'orr_status_id')->dropDownList(OrderRefundStatus::getList(), [
            'prompt' => '---'
        ]) ?>

        <?= $form->field($model, 'orr_client_currency')->dropDownList(Currency::getList(), [
            'prompt' => '---'
        ]) ?>

        <?= $form->field($model, 'orr_client_currency_rate')->input('number', [
            'step' => 'any',
        ]) ?>

        <?= $form->field($model, 'orr_client_selling_price')->input('number', [
            'step' => 'any',
        ]) ?>

        <?= $form->field($model, 'orr_client_penalty_amount')->input('number', [
            'step' => 'any',
        ]) ?>

        <?= $form->field($model, 'orr_client_processing_fee_amount')->input('number', [
            'step' => 'any',
        ]) ?>

        <?= $form->field($model, 'orr_client_charge_amount')->input('number', [
            'step' => 'any',
        ]) ?>

        <?= $form->field($model, 'orr_client_refund_amount')->input('number', [
            'step' => 'any',
        ]) ?>

        <?= $form->field($model, 'orr_description')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'orr_expiration_dt')->widget(\src\widgets\DateTimePicker::class) ?>

        <?= $form->field($model, 'orr_case_id')->textInput() ?>
    </div>
  <?php ActiveForm::end(); ?>
</div>
