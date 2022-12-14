<?php

use frontend\helpers\JsonHelper;
use kdn\yii2\JsonEditor;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefundStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-option-refund-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-2">
      <?= $form->errorSummary($model) ?>

      <?= $form->field($model, 'pqor_product_quote_refund_id')->input('number') ?>

      <?= $form->field($model, 'pqor_product_quote_option_id')->input('number') ?>

      <?= $form->field($model, 'pqor_selling_price')->input('number', ['step' => 'any']) ?>

      <?= $form->field($model, 'pqor_penalty_amount')->input('number', ['step' => 'any']) ?>

      <?= $form->field($model, 'pqor_processing_fee_amount')->input('number', ['step' => 'any']) ?>

      <?= $form->field($model, 'pqor_refund_amount')->input('number', ['step' => 'any']) ?>

      <?= $form->field($model, 'pqor_status_id')->dropDownList(ProductQuoteOptionRefundStatus::getList(), ['prompt' => '---']) ?>

      <div class="form-group">
          <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
      </div>
    </div>

  <div class="col-md-2">
      <?= $form->field($model, 'pqor_client_currency')->dropDownList(\common\models\Currency::getList(), ['prompt' => '---']) ?>

      <?= $form->field($model, 'pqor_client_currency_rate')->input('number', ['step' => 'any']) ?>

      <?= $form->field($model, 'pqor_client_selling_price')->input('number', ['step' => 'any']) ?>

      <?= $form->field($model, 'pqor_client_refund_amount')->input('number', ['step' => 'any']) ?>

      <?= $form->field($model, 'pqor_refund_allow')->checkbox() ?>
  </div>

  <div class="col-md-6">
      <?php
        try {
            $model->pqor_data_json = JsonHelper::encode($model->pqor_data_json);
            echo $form->field($model, 'pqor_data_json')->widget(
                JsonEditor::class,
                [
                  'clientOptions' => [
                      'modes' => ['code', 'form', 'tree', 'view'], //'text',
                      'mode' => $model->isNewRecord ? 'code' : 'form'
                  ],
                  //'collapseAll' => ['view'],
                  'expandAll' => ['tree', 'form'],
                ]
            );
        } catch (Exception $exception) {
            echo $form->field($model, 'pqor_data_json')->textarea(['rows' => 6]);
        }
        ?>
  </div>


    <?php ActiveForm::end(); ?>

</div>
