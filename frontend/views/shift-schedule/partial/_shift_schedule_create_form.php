<?php

/**
 * @var \modules\shiftSchedule\src\forms\ShiftScheduleCreateForm $model
 * @var \yii\web\View $this
 * @var \common\models\UserGroupAssign[] $usersGroupAssign
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
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use src\auth\Auth;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\JsExpression;
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

$pjaxId = 'pjax-add-event';
$formId = 'add-event-form';

$changeJs = <<<JS
(event) => {
    let userGroups = $(event.target).val();
    $('#getUsersByGroups').val(1);
    $('#submit-add-event').trigger('click');
    $('#submit-add-event').prop('disabled', true).addClass('disabled');
}
JS;
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

        <?= $form->field($model, 'getUsersByGroups')->hiddenInput(['id' => 'getUsersByGroups'])->label(false); ?>
        <?= $form->field($model, 'users')->hiddenInput(['id' => 'users'])->label(false); ?>

        <?= $form->field($model, 'userGroups')->widget(Select2::class, [
            'data' => $userGroups,
            'options' => [
                'multiple' => true,
            ],
            'size' => Select2::SMALL,
            'pluginEvents' => [
                'change' => new JsExpression($changeJs)
            ],
        ]) ?>

        <?php if ($usersGroupAssign) : ?>
            <div class="d-flex align-items-center justify-content-between" style="margin-bottom: 10px;">
                <label for="">Select Users</label>
                <div class="d-flex align-items-center align-content-center">
                    <span id="toggleAllUsers" data-check="true" style="margin-right: 10px;cursor: pointer;"><i class="far fa-check-circle"></i> Toggle All Users</span>
                    <input type="text" id="input-search" class="form-control" placeholder="Search for..." style="flex: 1;">
                </div>
            </div>
            <div id="group-wrapper">
                <?php $groupName = ''; ?>
                <ol class="list-group list-group-numbered">
                    <?php foreach ($usersGroupAssign as $groupName => $users) : ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center align-content-center">
                            <div style="width: 100%;">
                                <div class="d-flex align-items-center align-content-center justify-content-between" style="margin-bottom: 10px;">
                                    <div class="d-flex align-items-center align-content-center">
                                      <h6 style="margin-right: 10px; margin-bottom: 0;"><strong><?= $groupName ?></strong></h6>
                                      <span class="badge badge-primary badge-pill"><?= count($usersGroupAssign[$groupName]) ?></span>
                                    </div>
                                    <div>
                                      <span class="toggleGroupedUsers" data-check="true" style="margin-right: 10px;cursor: pointer;"><i class="far fa-check-circle"></i> Toggle Users</span>
                                    </div>
                                </div>
                                <div class="border user-wrapper">
                                    <?php foreach ($users as $userId => $userName) : ?>
                                        <label class="user-label" data-username="<?= $userName ?>" style="min-width: 125px;">
                                            <?= Html::checkbox('', $model->users ? in_array($userId, $model->getUsersBatch()) : false, ['value' => $userId, 'class' => 'input-users']) ?>
                                            <span class="input-username"><?= $userName ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        <?php endif; ?>

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
                        ],
                        'opens' => 'right'
                    ],
                    'pluginEvents' => [
                        'change' => new JsExpression($dateTimeRangeChangeJs)
                    ]
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'defaultDuration')->textInput(['id' => 'add-schedule-event-duration'])->label('Duration (HH:MM)') ?>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['cols' => 6, 'style' => 'resize:none; height:100px']) ?>

        <div class="modal-footer justify-content-center">
            <?= Html::submitButton('Submit', [
                'class' => 'btn btn-success',
                'id' => 'submit-add-event'
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
<?php
$js = <<<JS
$(document).off('change click', '.input-users').on('change click', '.input-users', function (e) {
let val = $(this).val();
$('.input-users[value="'+val+'"]').prop("checked", $(this).is(":checked"));
});
$(document).off('keyup change', '#input-search').on('keyup change', '#input-search', function (e) {
    let val = $(this).val();
    $('.user-label').hide();
    if (val === '') {
        $('.user-label').show();
    } else {
        $('.user-label[data-username*="'+val+'"], .user-label[data-username^="'+val+'"]').show();
    }
});
$(document).off('click', '#submit-add-event').on('click', '#submit-add-event', function (e) {
    e.preventDefault();
    let users = [];
    $.each($("input.input-users:checked"), function(){
        let val = $(this).val();
        if (!users.includes(val)) {
            users.push($(this).val());
        }
    });
    console.log(users.join());
    console.log($('#users'));
    $('#users').val(users.join());
    $('#$formId').submit();
});
$(document).off('click', '#toggleAllUsers').on('click', '#toggleAllUsers', function (e) {
    e.preventDefault();
    let isToggleCheck = $(this).data('check');
    $('.input-users').prop('checked', isToggleCheck);
    $(this).data('check', !isToggleCheck);
    $(this).find('i').toggleClass('far').toggleClass('fas');
    $('.toggleGroupedUsers').data('check', !isToggleCheck);
    if (!isToggleCheck) {
        $('.toggleGroupedUsers').find('i').addClass('far').removeClass('fas');
    } else {
        $('.toggleGroupedUsers').find('i').removeClass('far').addClass('fas');     
    }
});
$(document).off('click', '.toggleGroupedUsers').on('click', '.toggleGroupedUsers', function (e) {
    e.preventDefault();
    let isToggleCheck = $(this).data('check');
    $(this).closest('li').find('.input-users').prop('checked', isToggleCheck);
    $(this).data('check', !isToggleCheck);
    $(this).find('i').toggleClass('far').toggleClass('fas');
});
$(document).off('pjax:beforeSend', '#{$pjaxId}').on('pjax:beforeSend', '#{$pjaxId}', function (obj, xhr, data) {
    let btnObj = $('#submit-add-event');
    btnObj.html('<i class="fa fa-spin fa-spinner"></i>');
    btnObj.addClass('disabled').prop('disabled', true);
});
$(document).on('click', '#{$formId} .kv-clear', function (e) {
    e.preventDefault();
    let parentForm = $('#{$formId}');
    parentForm.find('.range-value').val('');
    parentForm.find('#shiftschedulecreateform-datetimerange').val('');
    parentForm.find('#add-schedule-event-duration').val('');
});
$(document).on('change', '#add-schedule-event-duration', function() {
    let parentForm = $('#{$formId}');
    let dateTimeRangeInput = parentForm.find('.range-value');
    let submitButton =  $('#submit-add-event');
    if($(this).val().trim()){
        let validTime = $(this).val().match(/^(\d+):[0-5][0-9]$/);
        if (!validTime) {
            $(this).val($(this).val()).focus().css('background', '#fdd');
            submitButton.prop('disabled', true);
        } else {
            let val = dateTimeRangeInput.val().split(' - ');
            let startDateTime = val[0] ? moment(new Date(val[0])).format('YYYY-MM-DD HH:mm') : moment(new Date()).format('YYYY-MM-DD HH:mm');
            let durationInSeconds = moment.duration($(this).val()).asSeconds()
            let newEndDateTime = moment(startDateTime, 'YYYY-MM-DD HH:mm').add(durationInSeconds, 'seconds').format('YYYY-MM-DD HH:mm');
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
            $('#shiftschedulecreateform-datetimerange').val(startDateTime + ' - ' + newEndDateTime);
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



