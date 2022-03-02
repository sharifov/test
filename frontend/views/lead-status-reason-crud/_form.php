<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadStatusReason\entity\LeadStatusReason */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-status-reason-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-2">

      <?= $form->field($model, 'lsr_key')->textInput(['maxlength' => true]) ?>

      <?= $form->field($model, 'lsr_name')->textInput(['maxlength' => true]) ?>

      <?= $form->field($model, 'lsr_description')->textarea(['maxlength' => true]) ?>

      <?= $form->field($model, 'lsr_enabled')->checkbox() ?>

      <?= $form->field($model, 'lsr_comment_required')->checkbox() ?>

      <div class="form-group">
          <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
      </div>

    </div>

    <div class="col-md-4">
        <?php
        try {
            echo $form->field($model, 'lsr_params')->widget(
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
            echo $form->field($model, 'lsr_params')->textarea(['rows' => 6]);
        }
        ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
