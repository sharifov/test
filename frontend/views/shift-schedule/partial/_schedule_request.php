<?php

/**
 * @var ScheduleRequestForm $scheduleRequestModel
 * @var bool $success
 */

use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use modules\shiftSchedule\src\forms\ScheduleRequestForm;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
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
                    'autocomplete' => 'off',
                    'data-pjax' => true,
                ],
            ]); ?>
            <div class="text-center js-loader"
                 style="display: none; position: absolute;width: 100%;height: 100%;background: rgba(255, 255, 255, .8);z-index: 9999;">
                <div class="spinner-border m-5" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
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
                        'data' => UserShiftScheduleHelper::getAvailableScheduleTypeList(),
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

            <div class="row">
                <div class="col-md-12 text-center">
                    <?= Html::submitButton('Send Request', [
                        'class' => [
                            'btn',
                            'btn-success',
                        ],
                    ]) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <input type="hidden" value="<?= $success ?>" id="request-status">
    </div>

<?php
Pjax::end();

$js = <<<JS
    $(document).on('pjax:beforeSend', function () {
        $('.js-loader').show();
    }).on('pjax:end', function (a, b, c) {
        $('.js-loader').hide();
        if (c.container === '#pjax-schedule-request') {
            $(document).trigger('ScheduleRequest:response', {
                requestStatus: $('#request-status').val()
            });
        }
    });
    
JS;
$this->registerJs($js);
