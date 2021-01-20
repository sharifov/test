<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceRecordingLog\ConferenceRecordingLog */
/* @var $form ActiveForm */
?>

<div class="conference-recording-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cfrl_id')->textInput() ?>

        <?= $form->field($model, 'cfrl_conference_sid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cfrl_user_id')->textInput() ?>

        <?= $form->field($model, 'cfrl_created_dt')->textInput() ?>

        <?= $form->field($model, 'cfrl_year')->textInput() ?>

        <?= $form->field($model, 'cfrl_month')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
