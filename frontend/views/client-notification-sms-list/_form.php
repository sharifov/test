<?php

use frontend\helpers\JsonHelper;
use sales\helpers\app\AppHelper;
use sales\model\client\notifications\sms\entity\Status;
use sales\widgets\DateTimePicker;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\client\notifications\sms\entity\ClientNotificationSmsList */
/* @var $form ActiveForm */

?>

<div class="client-notification-sms-list-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cnsl_status_id')->dropDownList(Status::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'cnsl_from_phone_id')->textInput() ?>

        <?= $form->field($model, 'cnsl_name_from')->textInput() ?>

        <?= $form->field($model, 'cnsl_to_client_phone_id')->textInput() ?>

        <?= $form->field($model, 'cnsl_start')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'cnsl_end')->widget(DateTimePicker::class) ?>

        <?php
        $model->cnsl_data_json = JsonHelper::encode($model->cnsl_data_json);
        try {
            echo $form->field($model, 'cnsl_data_json')->widget(
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
                echo $form->field($model, 'cnsl_data_json')->textarea(['rows' => 8, 'class' => 'form-control']);
            } catch (Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'ClientNotificationSmsList:_form:notValidJson');
            }
        }
        ?>

        <?= $form->field($model, 'cnsl_sms_id')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
