<?php

use sales\widgets\DateTimePicker;
use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceParticipantStats\ConferenceParticipantStats */
/* @var $form ActiveForm */
?>

<div class="conference-participant-stats-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cps_cf_id')->textInput() ?>

        <?= $form->field($model, 'cps_cf_sid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cps_participant_identity')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cps_user_id')->widget(UserSelect2Widget::class) ?>

        <?= $form->field($model, 'cps_created_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'cps_duration')->textInput() ?>

        <?= $form->field($model, 'cps_talk_time')->textInput() ?>

        <?= $form->field($model, 'cps_hold_time')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
