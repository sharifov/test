<?php

use common\models\ConferenceParticipant;
use frontend\extensions\DatePicker;
use sales\widgets\DateTimePicker;
use sales\widgets\UserSelect2Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ConferenceParticipant */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="conference-participant-form">

    <div class="col-md-4">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cp_cf_id')->textInput() ?>

    <?= $form->field($model, 'cp_cf_sid')->textInput() ?>

    <?= $form->field($model, 'cp_identity')->textInput() ?>

    <?= $form->field($model, 'cp_call_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cp_call_id')->textInput() ?>

    <?= $form->field($model, 'cp_user_id')->widget(UserSelect2Widget::class) ?>

    <?= $form->field($model, 'cp_status_id')->dropDownList(ConferenceParticipant::STATUS_LIST, ['prompt' => 'Select status']) ?>

    <?= $form->field($model, 'cp_join_dt')->widget(DateTimePicker::class) ?>

    <?= $form->field($model, 'cp_leave_dt')->widget(DateTimePicker::class) ?>

    <?= $form->field($model, 'cp_hold_dt')->widget(DateTimePicker::class) ?>

    <?= $form->field($model, 'cp_type_id')->dropDownList(ConferenceParticipant::TYPE_LIST, ['prompt' => 'Select type']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    </div>

</div>
