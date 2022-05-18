<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\QuoteCommunicationOpenLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-communication-open-log-form">
  <div class="row">
      <div class="col-md-6">
          <?php $form = ActiveForm::begin(); ?>

          <?= $form->field($model, 'qcol_quote_communication_id')->textInput() ?>

          <div class="form-group">
              <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
          </div>
        <?php ActiveForm::end(); ?>
      </div>
  </div>
</div>
