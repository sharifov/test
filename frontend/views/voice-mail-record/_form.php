<?php

use sales\widgets\DateTimePicker;
use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\voiceMailRecord\entity\VoiceMailRecord */
/* @var $form ActiveForm */
?>

<div class="voice-mail-record-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'vmr_call_id')->textInput() ?>

        <?= $form->field($model, 'vmr_record_sid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'vmr_client_id')->textInput() ?>

        <?= $form->field($model, 'vmr_user_id')->widget(UserSelect2Widget::class) ?>

        <?= $form->field($model, 'vmr_created_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'vmr_duration')->textInput() ?>

        <?= $form->field($model, 'vmr_new')->checkbox() ?>

        <?= $form->field($model, 'vmr_deleted')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
