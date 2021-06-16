<?php

use frontend\helpers\JsonHelper;
use sales\helpers\app\AppHelper;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\leadRequest\entity\LeadRequest */
/* @var $form ActiveForm */
?>

<div class="lead-request-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'lr_type')->dropDownList($model::TYPE_LIST, ['prompt' => '---'])?>

        <?= $form->field($model, 'lr_job_id')->textInput() ?>

        <?= $form->field($model, 'lr_lead_id')->textInput() ?>

        <?php
        try {
            $model->lr_json_data = JsonHelper::encode($model->lr_json_data);
            echo $form->field($model, 'lr_json_data')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form'],
                        'mode' => $model->isNewRecord ? 'code' : 'form'
                    ],
                    'expandAll' => ['tree', 'form'],
                ]
            );
        } catch (Exception $exception) {
            try {
                echo $form->field($model, 'lr_json_data')->textarea(['rows' => 8, 'class' => 'form-control']);
            } catch (Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'LeadRequestCrudController:_form:notValidJson');
            }
        }
        ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
