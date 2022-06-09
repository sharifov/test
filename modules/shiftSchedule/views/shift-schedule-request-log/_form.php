<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleRequestLog\ShiftScheduleRequestLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-schedule-request-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ssrh_ssr_id')->textInput() ?>

    <?= $form->field($model, 'ssrh_old_attr')->textInput() ?>

    <?= $form->field($model, 'ssrh_new_attr')->textInput() ?>

    <?= $form->field($model, 'ssrh_formatted_attr')->textInput() ?>

    <?= $form->field($model, 'ssrh_created_dt')->textInput() ?>

    <?= $form->field($model, 'ssrh_updated_dt')->textInput() ?>

    <?= $form->field($model, 'ssrh_created_user_id')->textInput() ?>

    <?= $form->field($model, 'ssrh_updated_user_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
