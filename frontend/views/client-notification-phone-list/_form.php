<?php

use frontend\helpers\JsonHelper;
use src\helpers\app\AppHelper;
use src\model\client\notifications\phone\entity\Status;
use src\widgets\DateTimePicker;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\client\notifications\phone\entity\ClientNotificationPhoneList */
/* @var $form ActiveForm */

?>

<div class="client-notification-phone-list-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cnfl_status_id')->dropDownList(Status::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'cnfl_from_phone_id')->textInput() ?>

        <?= $form->field($model, 'cnfl_to_client_phone_id')->textInput() ?>

        <?= $form->field($model, 'cnfl_start')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'cnfl_end')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'cnfl_from_hours')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cnfl_to_hours')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cnfl_message')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'cnfl_file_url')->textInput(['maxlength' => true]) ?>

        <?php
        $model->cnfl_data_json = JsonHelper::encode($model->cnfl_data_json);
        try {
            echo $form->field($model, 'cnfl_data_json')->widget(
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
                echo $form->field($model, 'cnfl_data_json')->textarea(['rows' => 8, 'class' => 'form-control']);
            } catch (Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'ClientNotificationPhoneList:_form:notValidJson');
            }
        }
        ?>

        <?= $form->field($model, 'cnfl_call_sid')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
