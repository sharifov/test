<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ConferenceParticipant */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="conference-participant-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cp_cf_id')->textInput() ?>

    <?= $form->field($model, 'cp_call_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cp_call_id')->textInput() ?>

    <?= $form->field($model, 'cp_status_id')->textInput() ?>

    <?= $form->field($model, 'cp_join_dt')->textInput() ?>

    <?= $form->field($model, 'cp_leave_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
