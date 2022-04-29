<?php

/**
 * @var ScheduleRequestForm $scheduleRequestModel
 */

use kartik\select2\Select2;
use kartik\time\TimePicker;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\forms\ScheduleRequestForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>

<?php Pjax::begin([
    'id' => 'pjax-schedule-request',
    'enablePushState' => false,
    'enableReplaceState' => false,
]); ?>

<div class="user-shift-schedule-form">
    <div class="col-md-12">
        <?php $form = ActiveForm::begin([
            'id' => 'schedule-request-form',
            'options' => [
                'data-pjax' => true,
            ],
        ]); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($scheduleRequestModel, 'startDt')
                    ->textInput([
                        'type' => 'date',
                        'min' => date('Y-m-d'),
                        'max' => date('Y-m-d', strtotime('+1 year')),
                    ])
                    ->label(Yii::t('schedule-request', 'From'))?>
            </div>
            <div class="col-md-6">
                <?= $form->field($scheduleRequestModel, 'duration')
                    ->dropDownList(
                        array_combine(
                            range($scheduleRequestModel::MIN_DAYS_DURATION, $scheduleRequestModel::MAX_DAYS_DURATION),
                            range($scheduleRequestModel::MIN_DAYS_DURATION, $scheduleRequestModel::MAX_DAYS_DURATION)
                        )
                    )
                    ->label(Yii::t('schedule-request', 'Duration (days)')) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($scheduleRequestModel, 'startTime')->widget(TimePicker::class, [
                    'pluginOptions' => [
                        'showSeconds' => false,
                        'showMeridian' => false,
                        'minuteStep' => 1,
                        'secondStep' => 5,
                    ]
                ])->label(Yii::t('schedule-request', 'Start Time (Local)')) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($scheduleRequestModel, 'endTime')->widget(TimePicker::class, [
                    'pluginOptions' => [
                        'showSeconds' => false,
                        'showMeridian' => false,
                        'minuteStep' => 1,
                        'secondStep' => 5,
                    ]
                ])->label(Yii::t('schedule-request', 'End Time (Local)')) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($scheduleRequestModel, 'scheduleType')->widget(Select2::class, [
                    'data' => ShiftScheduleType::getList(true),
                    'size' => Select2::SMALL,
                    'options' => [
                        'placeholder' => Yii::t('schedule-request', 'Select Schedule Type'),
                        'multiple' => false,
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($scheduleRequestModel, 'description')
                    ->textarea([
                        'rows' => 3,
                        'maxLength' => $scheduleRequestModel::DESCRIPTION_MAX_LENGTH,
                    ]) ?>
            </div>
        </div>

        <?= Html::submitButton('Send Request', [
            'class' => [
                'btn',
                'btn-success',
            ],
        ]) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php Pjax::end(); ?>