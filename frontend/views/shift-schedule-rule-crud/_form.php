<?php

use common\models\Employee;
use frontend\widgets\cronExpression\CronExpressionWidget;
use kartik\time\TimePicker;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\widget\ShiftSelectWidget;
use yii\bootstrap4\Html;
use yii\bootstrap4\Tabs;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ShiftScheduleRule */
/* @var $form ActiveForm */
?>

<div class="shift-schedule-rule-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-12">
        <?php echo $form->errorSummary($model) ?>
    </div>

    <div class="col-md-4">


        <?= $form->field($model, 'ssr_title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ssr_shift_id')->widget(ShiftSelectWidget::class) ?>

        <?= $form->field($model, 'ssr_sst_id')->dropDownList(ShiftScheduleType::getList(), ['prompt' => '-']) ?>


        <div class="row">
            <div class="col-md-6">
            <?= $form->field($model, 'ssr_start_time_loc')->widget(TimePicker::class, [
                'pluginOptions' => [
                    'showSeconds' => false,
                    'showMeridian' => false,
                    'minuteStep' => 1,
                    'secondStep' => 5,
                ]
            ])->label('Start Time (Local)') ?>
            </div>
            <div class="col-md-6">
            <?php /*= $form->field($model, 'ssr_end_time_loc')->widget(TimePicker::class, [
                'pluginOptions' => [
                    'showSeconds' => true,
                    'showMeridian' => false,
                    'minuteStep' => 1,
                    'secondStep' => 5,
                ]
            ])->label('End Time (Local)');*/ ?>
                <?= $form->field($model, 'ssr_duration_time')->input('number', ['maxlength' => true, 'min' => 1])
                ->label('Duration Time (minutes)')?>
            </div>
        </div>


        <?php
        echo $form->field($model, 'ssr_timezone')->widget(\kartik\select2\Select2::class, [
            'data' => Employee::timezoneList(true),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['placeholder' => 'Select TimeZone', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ]);
        ?>



        <?= $form->field($model, 'ssr_enabled')->checkbox() ?>

        <?php /*= $form->field($model, 'ssr_start_time_utc')->widget(TimePicker::class, [
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
        ])*/ ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    </div>

    <div class="col-md-8">


        <?php
            echo Tabs::widget([
                'items' => [
                    [
                        'label' => 'Cron Expression',
                        'content' => '<div class="col-md-12 bg-white">' .
                            $form->field($model, 'ssr_cron_expression')->widget(CronExpressionWidget::class, [
                            'options' => ['year' => false]])->label(false) . '</div>',
                    ],
                    [
                        'label' => 'Expression Exclude',
                        'content' => '<div class="col-md-12 bg-white">' .
                            $form->field($model, 'ssr_cron_expression_exclude')->widget(CronExpressionWidget::class)
                            ->label(false) . '</div>',
                    ],
                ]
            ]);
            ?>

        <?php /*= $form->field($model, 'ssr_cron_expression')->widget(CronExpressionWidget::class, [
        'options' => ['year' => false]]); ?>

        <?= $form->field($model, 'ssr_cron_expression_exclude')->widget(CronExpressionWidget::class)*/ ?>
    </div>
  <?php ActiveForm::end(); ?>

</div>
