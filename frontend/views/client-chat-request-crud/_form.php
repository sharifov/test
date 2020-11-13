<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatRequest\entity\ClientChatRequest */
/* @var $form ActiveForm */
?>

<div class="client-chat-request-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->errorSummary($model) ?>

        <?= $form->field($model, 'ccr_event')->dropDownList(\sales\model\clientChatRequest\entity\ClientChatRequest::getEventList(), ['prompt' => '-']) ?>

        <?php
        try {
            echo $form->field($model, 'ccr_json_data')->widget(
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
            echo $form->field($model, 'ccr_json_data')->textarea(['rows' => 6]);
        }
        ?>

        <?= $form->field($model, 'ccr_job_id')->textInput() ?>

        <?php // $form->field($model, 'ccr_created_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
