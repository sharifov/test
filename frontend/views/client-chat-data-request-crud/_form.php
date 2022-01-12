<?php

use frontend\helpers\JsonHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\clientChatDataRequest\entity\ClientChatDataRequest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-data-request-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
      <div class="col-md-2">
          <?= $form->field($model, 'ccdr_chat_id')->input('number') ?>

          <div class="form-group">
              <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
          </div>
      </div>
      <div class="col-md-6">
          <?php
            try {
                $model->ccdr_data_json = JsonHelper::encode($model->ccdr_data_json);
                echo $form->field($model, 'ccdr_data_json')->widget(
                    \kdn\yii2\JsonEditor::class,
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
                echo $form->field($model, 'ccdr_data_json')->textarea(['rows' => 6]);
            }
            ?>
      </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
