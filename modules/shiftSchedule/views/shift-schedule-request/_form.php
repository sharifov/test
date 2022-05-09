<?php

/**
 * @var View $this
 * @var ShiftScheduleRequest $model
 * @var ActiveForm $form
 */

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;

?>

<div class="shift-schedule-request-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ssr_uss_id')->textInput() ?>

    <?= $form->field($model, 'ssr_sst_id')->textInput() ?>

    <?= $form->field($model, 'ssr_status_id')->textInput() ?>

    <?= $form->field($model, 'ssr_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ssr_created_dt')->textInput() ?>

    <?= $form->field($model, 'ssr_update_dt')->textInput() ?>

    <?= $form->field($model, 'ssr_created_user_id')->textInput() ?>

    <?= $form->field($model, 'ssr_updated_user_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
