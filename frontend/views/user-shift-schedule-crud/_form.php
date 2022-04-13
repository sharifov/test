<?php

use frontend\widgets\DateTimePickerWidget;
use kartik\select2\Select2;
use src\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule;
use src\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule */
/* @var $form ActiveForm */
?>

<div class="user-shift-schedule-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'uss_user_id')->widget(UserSelect2Widget::class) ?>

        <?= $form->field($model, 'uss_sst_id')->widget(Select2::class, [
            'data' => \modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType::getList(),
            'size' => Select2::SMALL,
            'options' => ['placeholder' => 'Select Schedule Type', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>

        <?= $form->field($model, 'uss_status_id')->dropDownList(UserShiftSchedule::getStatusList(), [
            'prompt' => '---'
        ]) ?>

        <?= $form->field($model, 'uss_type_id')->dropDownList(UserShiftSchedule::getTypeList(), [
            'prompt' => '---'
        ]) ?>

        <?php /*echo $form->field($model, 'uss_ssr_id')->textInput()*/ ?>

        <?= $form->field($model, 'uss_shift_id')->widget(Select2::class, [
            'data' => \src\model\shiftSchedule\entity\shift\Shift::getList(),
            'size' => Select2::SMALL,
            'options' => ['placeholder' => 'Select Shift', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>

        <?= $form->field($model, 'uss_ssr_id')->widget(Select2::class, [
            'data' => \src\model\shiftSchedule\entity\shiftScheduleRule\ShiftScheduleRule::getList(),
            'size' => Select2::SMALL,
            'options' => ['placeholder' => 'Select Shift', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>



        <?php /*= $form->field($model, 'uss_sst_id')->dropDownList(\modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType::getList(), [
            'prompt' => '---'
        ])*/ ?>

        <?php /*= $form->field($model, 'uss_shift_id')->textInput()*/ ?>

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




        <?= $form->field($model, 'uss_customized')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
