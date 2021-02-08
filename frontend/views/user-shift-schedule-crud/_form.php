<?php

use frontend\widgets\DateTimePickerWidget;
use sales\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule;
use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule */
/* @var $form ActiveForm */
?>

<div class="user-shift-schedule-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'uss_user_id')->widget(UserSelect2Widget::class) ?>

        <?= $form->field($model, 'uss_shift_id')->textInput() ?>

        <?= $form->field($model, 'uss_ssr_id')->textInput() ?>

        <?= $form->field($model, 'uss_description')->textarea(['maxlength' => true]) ?>

        <?= $form->field($model, 'uss_start_utc_dt')->widget(DateTimePickerWidget::class, [
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true
            ]
        ]) ?>

        <?= $form->field($model, 'uss_end_utc_dt')->widget(DateTimePickerWidget::class, [
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true
            ]
        ]) ?>

        <?= $form->field($model, 'uss_duration')->textInput() ?>

        <?= $form->field($model, 'uss_status_id')->dropDownList(UserShiftSchedule::getStatusList(), [
            'prompt' => '---'
        ]) ?>

        <?= $form->field($model, 'uss_type_id')->dropDownList(UserShiftSchedule::getTypeList(), [
            'prompt' => '---'
        ]) ?>

        <?= $form->field($model, 'uss_customized')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
