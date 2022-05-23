<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-shift-schedule-log-form">
    <div class="row">
        <div class="col-md-3">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'ussl_id')->textInput() ?>

            <?= $form->field($model, 'ussl_uss_id')->textInput() ?>

            <?= $form->field($model, 'ussl_old_attr')->textInput() ?>

            <?= $form->field($model, 'ussl_new_attr')->textInput() ?>

            <?= $form->field($model, 'ussl_formatted_attr')->textInput() ?>

            <?= $form->field($model, 'ussl_created_user_id')->textInput() ?>

            <?= $form->field($model, 'ussl_created_dt')->textInput() ?>

            <?= $form->field($model, 'ussl_month_start')->textInput() ?>

            <?= $form->field($model, 'ussl_year_start')->textInput() ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
