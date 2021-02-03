<?php

use kartik\time\TimePicker;
use sales\model\shiftSchedule\widget\ShiftSelectWidget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\shiftSchedule\entity\shiftScheduleRule\ShiftScheduleRule */
/* @var $form ActiveForm */
?>

<div class="shift-schedule-rule-form">

    <div class="col-md-3">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ssr_shift_id')->widget(ShiftSelectWidget::class) ?>

        <?= $form->field($model, 'ssr_title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ssr_timezone')->textInput() ?>

        <?= $form->field($model, 'ssr_start_time_loc')->widget(TimePicker::class, [
            'pluginOptions' => [
                'showSeconds' => true,
                'showMeridian' => false,
                'minuteStep' => 1,
                'secondStep' => 5,
            ]
        ]) ?>

        <?= $form->field($model, 'ssr_end_time_loc')->widget(TimePicker::class, [
            'pluginOptions' => [
                'showSeconds' => true,
                'showMeridian' => false,
                'minuteStep' => 1,
                'secondStep' => 5,
            ]
        ]) ?>

        <?= $form->field($model, 'ssr_duration_time')->input('number', ['maxlength' => true]) ?>

        <?= $form->field($model, 'ssr_cron_expression')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ssr_cron_expression_exclude')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ssr_enabled')->checkbox() ?>

        <?= $form->field($model, 'ssr_start_time_utc')->widget(TimePicker::class, [
            'pluginOptions' => [
                'showSeconds' => true,
                'showMeridian' => false,
                'minuteStep' => 1,
                'secondStep' => 5,
            ]
        ]) ?>

        <?= $form->field($model, 'ssr_end_time_utc')->widget(TimePicker::class, [
            'pluginOptions' => [
                'showSeconds' => true,
                'showMeridian' => false,
                'minuteStep' => 1,
                'secondStep' => 5,
            ]
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
