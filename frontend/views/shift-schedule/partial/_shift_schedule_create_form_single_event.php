<?php

/**
 * @var \modules\shiftSchedule\src\forms\SingleEventCreateForm $singleEventForm
 * @var \yii\web\View $this
 */

use common\models\query\UserGroupQuery;
use common\models\UserGroup;
use frontend\extensions\DateRangePicker;
use frontend\widgets\DateTimePickerWidget;
use kartik\select2\Select2;
use kartik\time\TimePicker;
use modules\shiftSchedule\src\abac\dto\ShiftAbacDto;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\forms\SingleEventCreateForm;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use src\auth\Auth;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\JsExpression;
use yii\widgets\Pjax;

$user = Auth::user();

$pjaxId = 'pjax-add-event-single-user';
$formId = 'add-event-form-singe-user';

$dateTimeRangeChangeJs = <<<JS
(event) => {
    let val = $(event.target).val().split(' - ');
    if (val) {
      let startDateTime = val[0] ? moment(new Date(val[0])) : null;
      let endDateTime = val[1] ? moment(new Date(val[1])) : null;
      var diffInHours = moment.duration(moment.duration(endDateTime.diff(startDateTime)).asHours(), 'hours');
      var hours = Math.floor(diffInHours.asHours());
      var minutes  = Math.floor(diffInHours.asMinutes()) - hours * 60;
      var duration = ((hours > 9) ? hours : ('0' + hours)) + ':' + ((minutes > 9) ? minutes : ('0' + minutes));
      $('#add-single-schedule-event-duration').val(duration);
    }
}
JS;

$statusList = [];
foreach (UserShiftSchedule::getStatusList() as $statusId => $statusName) {
    $dto = new ShiftAbacDto();
    $dto->setStatus((int)$statusId);
    if (Yii::$app->abac->can($dto, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_ACCESS)) {
        $statusList[$statusId] = $statusName;
    }
}

?>

<script>pjaxOffFormSubmit('#<?= $pjaxId ?>')</script>
<div class="row">
    <div class="col-md-12">
        <?php Pjax::begin(['id' => $pjaxId, 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 2000]) ?>

        <?php $form = ActiveForm::begin(['id' => $formId, 'options' => [
            'data-pjax' => 1
        ], 'enableAjaxValidation' => false, 'enableClientValidation' => false]); ?>

        <?= $form->errorSummary($singleEventForm) ?>

        <?= $form->field($singleEventForm, 'userId')->hiddenInput()->label(false); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($singleEventForm, 'status')->dropdownList($statusList, ['prompt' => '---']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($singleEventForm, 'scheduleType')->dropdownList(UserShiftScheduleHelper::getAvailableScheduleTypeList(), ['prompt' => '---']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($singleEventForm, 'dateTimeRange')->widget(DateRangePicker::class, [
                    'presetDropdown' => false,
                    'hideInput' => true,
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'timePicker' => true,
                        'timePickerIncrement' => 1,
                        'timePicker24Hour' => true,
                        'locale' => [
                            'format' => 'Y-m-d H:i',
                            'separator' => ' - '
                        ],
                        'opens' => 'right'
                    ],
                    'pluginEvents' => [
                        'change' => new JsExpression($dateTimeRangeChangeJs)
                    ],
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($singleEventForm, 'defaultDuration')->textInput(['id' => 'add-single-schedule-event-duration'])->label('Duration (HH:MM)') ?>
            </div>
        </div>

        <?= $form->field($singleEventForm, 'description')->textarea(['cols' => 6, 'style' => 'resize:none; height:100px']) ?>

        <div class="modal-footer justify-content-center">
            <?= Html::submitButton('Submit', [
                'class' => 'btn btn-success',
                'id' => 'submit-add-event-single-user'
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <?php
        $js = <<<JS
$(document).off('pjax:beforeSend', '#{$pjaxId}').on('pjax:beforeSend', '#{$pjaxId}', function (obj, xhr, data) {
    let btnObj = $('#submit-add-event-single-user');
    btnObj.html('<i class="fa fa-spin fa-spinner"></i>');
    btnObj.addClass('disabled').prop('disabled', true);
});
$(document).on('click', '#{$formId} .kv-clear', function (e) {
    e.preventDefault();
    let parentForm = $('#{$formId}');
    parentForm.find('.range-value').val('');
    parentForm.find('#add-single-schedule-event-duration').val('');
});
$(document).on('change', '#add-single-schedule-event-duration', function() {
    let parentForm = $('#{$formId}');
    let dateTimeRangeInput = parentForm.find('.range-value');
    let submitButton =  $('#submit-add-event-single-user');
    if($(this).val().trim()){
        let validTime = $(this).val().match(/^(\d+):[0-5][0-9]$/);
        if (!validTime) {
            $(this).val($(this).val()).focus().css('background', '#fdd');
            submitButton.prop('disabled', true);
        } else {
            if (dateTimeRangeInput.val().trim() !== '') {
                let val = dateTimeRangeInput.val().split(' - ');
                let durationInSeconds = moment.duration($(this).val()).asSeconds()
                let startDateTime = val[0] ? moment(new Date(val[0])).format('YYYY-MM-DD HH:mm') : null;
                var newEndDateTime = moment(startDateTime, 'YYYY-MM-DD HH:mm').add(durationInSeconds, 'seconds').format('YYYY-MM-DD HH:mm');
                $('.range-value').daterangepicker({
                    timePicker: true,
                    startDate: startDateTime,
                    endDate: newEndDateTime,
                    timePickerIncrement: 1,
                    timePicker24Hour: true,
                    locale: {
                        format: 'YYYY-MM-DD HH:mm',
                        separator: ' - '
                    },
                    opens: 'right'
                });
            }
            $(this).css('background', 'transparent');
            submitButton.prop('disabled', false);
        }
    } else {
        $(this).css('background', 'transparent');
        submitButton.prop('disabled', false);
    }
});
JS;
        $this->registerJs($js);
        ?>

        <?php Pjax::end() ?>
    </div>
</div>
