<?php

/**
 * @var ScheduleRequestForm $scheduleRequestModel
 */

use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
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
                <div class="col-md-12">
                    <?= $form->field($scheduleRequestModel, 'requestedRangeTime', [
                        'options' => ['class' => 'form-group']
                    ])->widget(DateRangePicker::class, [
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'timePicker' => true,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'Y-m-d H:i',
                                'separator' => ' - '
                            ],
                        ]
                    ]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($scheduleRequestModel, 'scheduleType')->widget(Select2::class, [
                        'data' => ShiftScheduleType::getList(true),
                        'size' => Select2::SMALL,
                        'options' => [
                            'placeholder' => 'Select Schedule Type',
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