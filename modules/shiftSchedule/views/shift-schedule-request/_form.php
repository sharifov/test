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

    <?= $form->field($model, 'srh_uss_id')->textInput() ?>

    <?= $form->field($model, 'srh_sst_id')->textInput() ?>

    <?= $form->field($model, 'srh_status_id')->textInput() ?>

    <?= $form->field($model, 'srh_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'srh_created_dt')->textInput() ?>

    <?= $form->field($model, 'srh_update_dt')->textInput() ?>

    <?= $form->field($model, 'srh_created_user_id')->textInput() ?>

    <?= $form->field($model, 'srh_updated_user_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
