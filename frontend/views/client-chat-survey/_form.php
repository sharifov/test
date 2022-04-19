<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ClientChatSurvey;

/* @var $this yii\web\View */
/* @var $model ClientChatSurvey */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-survey-form">
  <div class="row">
      <div class="col-md-4">
          <?php $form = ActiveForm::begin(); ?>

          <?= $form->field($model, 'ccs_type')->dropDownList(ClientChatSurvey::TYPE_LIST) ?>

          <?= $form->field($model, 'ccs_trigger_source')->dropDownList(ClientChatSurvey::TRIGGER_SOURCE_LIST) ?>

          <?= $form->field($model, 'ccs_uid')->textInput() ?>

          <?= $form->field($model, 'ccs_chat_id')->textInput() ?>

          <?= $form->field($model, 'ccs_template')->textInput() ?>

          <?= $form->field($model, 'ccs_requested_by')->textInput() ?>

          <?= $form->field($model, 'ccs_requested_for')->textInput() ?>

          <?= $form->field($model, 'ccs_status')->dropDownList(ClientChatSurvey::STATUS_LIST) ?>

          <div class="form-group">
              <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
          </div>
        <?php ActiveForm::end(); ?>
      </div>
  </div>
</div>
