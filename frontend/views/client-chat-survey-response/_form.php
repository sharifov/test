<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ClientChatSurveyResponse;

/* @var $this yii\web\View */
/* @var $model ClientChatSurveyResponse */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-survey-response-form">
  <div class="row">
      <div class="col-md-4">
          <?php $form = ActiveForm::begin(); ?>

          <?= $form->field($model, 'ccsr_question')->textarea(['rows' => 6]) ?>

          <?= $form->field($model, 'ccsr_response')->textarea(['rows' => 6]) ?>

          <div class="form-group">
              <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
          </div>
        <?php ActiveForm::end(); ?>
      </div>
  </div>
</div>
