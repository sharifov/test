<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\emailQuote\entity\EmailQuote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-quote-form">
  <div class="row">
      <div class="col-md-2">
          <?php $form = ActiveForm::begin(); ?>

          <?= $form->field($model, 'eq_email_id')->textInput() ?>

          <?= $form->field($model, 'eq_quote_id')->textInput() ?>

          <div class="form-group">
              <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
          </div>
        <?php ActiveForm::end(); ?>
      </div>
  </div>
</div>
