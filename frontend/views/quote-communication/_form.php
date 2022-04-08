<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\QuoteCommunication;

/* @var $this yii\web\View */
/* @var $model QuoteCommunication */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-quote-form">
  <div class="row">
      <div class="col-md-2">
          <?php $form = ActiveForm::begin(); ?>

          <?= $form->field($model, 'qc_communication_type')->textInput() ?>

          <?= $form->field($model, 'qc_communication_id')->textInput() ?>

          <?= $form->field($model, 'qc_quote_id')->textInput() ?>

          <div class="form-group">
              <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
          </div>
        <?php ActiveForm::end(); ?>
      </div>
  </div>
</div>
