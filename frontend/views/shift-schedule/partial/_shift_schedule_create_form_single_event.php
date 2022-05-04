<?php

/**
 * @var \modules\shiftSchedule\src\forms\SingleEventCreateForm $singleEventForm
 * @var \yii\web\View $this
 */

use common\models\query\UserGroupQuery;
use common\models\UserGroup;
use frontend\widgets\DateTimePickerWidget;
use kartik\select2\Select2;
use kartik\time\TimePicker;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\forms\SingleEventCreateForm;
use src\auth\Auth;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

$user = Auth::user();

$pjaxId = 'pjax-add-event-single-user';
$formId = 'add-event-form-singe-user';
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
                <?= $form->field($singleEventForm, 'status')->dropdownList(UserShiftSchedule::getStatusList(), ['prompt' => '---']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($singleEventForm, 'scheduleType')->dropdownList(ShiftScheduleType::getList(true), ['prompt' => '---']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($singleEventForm, 'startDateTime')->widget(DateTimePickerWidget::class) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($singleEventForm, 'duration')->widget(TimePicker::class, [
                    'pluginOptions' => [
                        'showSeconds' => false,
                        'showMeridian' => false,
                        'minuteStep' => 1,
                        'defaultTime' => false
                    ]
                ]) ?>
            </div>
        </div>

        <?= $form->field($singleEventForm, 'description')->textarea(['cols' => 6]) ?>

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
JS;
        $this->registerJs($js);
        ?>

        <?php Pjax::end() ?>
    </div>
</div>
