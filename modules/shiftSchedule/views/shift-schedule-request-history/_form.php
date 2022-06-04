<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\ShiftScheduleRequestHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-schedule-request-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ssrh_ssr_id')->textInput() ?>

    <?= $form->field($model, 'ssrh_from_status_id')->textInput() ?>

    <?= $form->field($model, 'ssrh_to_status_id')->textInput() ?>

    <?= $form->field($model, 'ssrh_from_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ssrh_to_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ssrh_created_dt')->textInput() ?>

    <?= $form->field($model, 'ssrh_updated_dt')->textInput() ?>

    <?= $form->field($model, 'ssrh_created_user_id')->textInput() ?>

    <?= $form->field($model, 'ssrh_updated_user_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
