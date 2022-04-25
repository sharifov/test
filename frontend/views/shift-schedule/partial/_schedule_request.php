<?php

/**
 * @var ScheduleRequestForm $scheduleRequestModel
 */

use kartik\select2\Select2;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\widgets\DateTimePickerWidget;
use frontend\models\form\shiftSchedule\ScheduleRequestForm;
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
                <?= $form->field($scheduleRequestModel, 'startDt')->widget(DateTimePickerWidget::class, [
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd hh:ii:ss',
                        'todayBtn' => true,
                        'startDate' => date('Y-m-d h:i:m'),
                    ]
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($scheduleRequestModel, 'endDt')->widget(DateTimePickerWidget::class, [
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd hh:ii:ss',
                        'todayBtn' => true,
                        'startDate' => date('Y-m-d h:i:m'),
                    ]
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($scheduleRequestModel, 'scheduleType')->widget(Select2::class, [
                    'data' => ShiftScheduleType::getList(),
                    'size' => Select2::SMALL,
                    'options' => ['placeholder' => 'Select Schedule Type', 'multiple' => false],
                    'pluginOptions' => ['allowClear' => true],
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