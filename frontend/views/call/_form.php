<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Call */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">

    <?= $form->field($model, 'c_call_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_call_type_id')->dropDownList(\common\models\Call::CALL_TYPE_LIST) ?>

    <?= $form->field($model, 'c_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_to')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_call_status')->dropDownList(\common\models\Call::TW_STATUS_LIST) ?>

    <?= $form->field($model, 'c_status_id')->dropDownList(\common\models\Call::STATUS_LIST) ?>

    <?= $form->field($model, 'c_forwarded_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_caller_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_parent_call_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_call_duration')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6">

    <?= $form->field($model, 'c_recording_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_recording_duration')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_sequence_number')->input('number') ?>

    <?= $form->field($model, 'c_lead_id')->textInput() ?>

    <?php //= $form->field($model, 'c_created_user_id')->textInput() ?>

    <?php //= $form->field($model, 'c_created_dt')->textInput() ?>

    <?= $form->field($model, 'c_com_call_id')->textInput() ?>

    <?php //= $form->field($model, 'c_updated_dt')->textInput() ?>

    <?= $form->field($model, 'c_project_id')->dropDownList(\common\models\Project::getList()) ?>

    <?= $form->field($model, 'c_error_message')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_is_new')->checkbox() ?>

    <?= $form->field($model, 'c_is_deleted')->checkbox() ?>
</div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
