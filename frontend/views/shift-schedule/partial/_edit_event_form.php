<?php

/**
 * @var $this \yii\web\View
 * @var $model ShiftScheduleEditForm
 */

use common\models\UserGroup;
use kartik\daterange\DateRangePicker;
use modules\shiftSchedule\src\abac\dto\ShiftAbacDto;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\forms\ShiftScheduleEditForm;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use src\auth\Auth;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$user = Auth::user();

$userGroupsList = UserGroup::getList();
$userGroups = [];
foreach ($userGroupsList as $groupId => $groupName) {
    $dto = new ShiftAbacDto();
    $dto->setGroup($groupId);
    if (Yii::$app->abac->can($dto, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_ACCESS)) {
        $userGroups[$groupId] = $groupName;
    }
}

$statusList = [];
foreach (UserShiftSchedule::getStatusList() as $statusId => $statusName) {
    $dto = new ShiftAbacDto();
    $dto->setStatus((int)$statusId);
    if (Yii::$app->abac->can($dto, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_ACCESS)) {
        $statusList[$statusId] = $statusName;
    }
}

$pjaxId = 'pjax-edit-event';
$formId = 'edit-event-form';

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
        $('#add-schedule-event-duration').val(duration);
    }
}
JS;
?>

<script>pjaxOffFormSubmit('#<?= $pjaxId ?>')</script>
<div class="row">
    <div class="col-md-12">
        <?php Pjax::begin(['id' => $pjaxId, 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 2000]) ?>

        <?php $form = ActiveForm::begin(['id' => $formId, 'options' => [
            'data-pjax' => 1
        ], 'enableAjaxValidation' => false, 'enableClientValidation' => false]); ?>

        <?= $form->errorSummary($model) ?>

        <?= $form->field($model, 'eventId')->hiddenInput()->label(false); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'status')->dropdownList($statusList, ['prompt' => '---']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'scheduleType')->dropdownList(UserShiftScheduleHelper::getAvailableScheduleTypeList(), ['prompt' => '---']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'dateTimeRange')->widget(DateRangePicker::class, [
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
                        ]
                    ],
                    'pluginEvents' => [
                        'change' => new JsExpression($dateTimeRangeChangeJs)
                    ]
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'duration')->textInput(['id' => 'add-schedule-event-duration'])->label('Duration (HH:MM)')?>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['cols' => 6, 'style' => 'resize:none; height:100px']) ?>

        <div class="modal-footer justify-content-center">
            <?= Html::submitButton('Submit', [
                'class' => 'btn btn-success',
                'id' => 'submit-edit-event'
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <?php Pjax::end() ?>
    </div>
</div>

<?php
$js = <<<JS
$(document).off('pjax:beforeSend', '#{$pjaxId}').on('pjax:beforeSend', '#{$pjaxId}', function (obj, xhr, data) {
    let btnObj = $('#submit-edit-event');
    btnObj.html('<i class="fa fa-spin fa-spinner"></i>');
    btnObj.addClass('disabled').prop('disabled', true);
});

$(document).on('click', '#{$formId} .kv-clear', function (e) {
    e.preventDefault();
    let parentForm = $('#{$formId}');
    parentForm.find('.range-value').val('');
    parentForm.find('#shiftscheduleeditform-datetimerange').val('');
    parentForm.find('#add-schedule-event-duration').val('');
});
$(document).on('change', '#add-schedule-event-duration', function() {
    let parentForm = $('#{$formId}');
    let dateTimeRangeInput = parentForm.find('.range-value');
    let submitButton =  $('#submit-edit-event');
    if($(this).val().trim()){
        let validTime = $(this).val().match(/^(\d+):[0-5][0-9]$/);
        if (!validTime) {
            $(this).val($(this).val()).focus().css('background', '#fdd');
            submitButton.prop('disabled', true);
        } else {
            let val = dateTimeRangeInput.val().split(' - ');
            let durationInSeconds = moment.duration($(this).val()).asSeconds()
            let startDateTime = val[0] ? moment(new Date(val[0])).format('YYYY-MM-DD HH:mm') : moment(new Date()).format('YYYY-MM-DD HH:mm');
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
            $('#shiftscheduleeditform-datetimerange').val(startDateTime + ' - ' + newEndDateTime);
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




