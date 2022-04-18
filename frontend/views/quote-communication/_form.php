<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\QuoteCommunication;
use frontend\models\CommunicationForm;
use kdn\yii2\JsonEditor;

/* @var $this yii\web\View */
/* @var $model QuoteCommunication */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-communication-form">
  <div class="row">
      <div class="col-md-6">
          <?php $form = ActiveForm::begin(); ?>

          <?= $form->field($model, 'qc_uid')->textInput() ?>

          <?= $form->field($model, 'qc_communication_type')->dropDownList(CommunicationForm::TYPE_LIST) ?>

          <?= $form->field($model, 'qc_communication_id')->textInput() ?>

          <?= $form->field($model, 'qc_quote_id')->textInput() ?>

          <?php try {
              echo $form
                  ->field($model, 'qc_ext_data')
                  ->widget(
                      JsonEditor::class,
                      [
                          'clientOptions' => [
                              'modes' => ['code', 'form', 'tree', 'view'],
                              'mode' => $model->isNewRecord ? 'code' : 'form'
                          ],
                          'expandAll' => ['tree', 'form'],
                          'value' => (is_null($model->qc_ext_data)) ? '{}' : $model->qc_ext_data
                      ]
                  );
          } catch (Exception $exception) {
              echo $form->field($model, 'qc_ext_data')->textarea(['rows' => 6, 'value' => $model->qc_ext_data]);
          } ?>

          <div class="form-group">
              <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
          </div>
        <?php ActiveForm::end(); ?>
      </div>
  </div>
</div>
