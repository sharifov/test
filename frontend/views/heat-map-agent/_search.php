<?php

use common\models\Employee;
use common\models\UserGroup;
use modules\shiftSchedule\src\entities\shift\ShiftQuery;
use src\auth\Auth;
use src\model\lead\reports\HeatMapLeadSearch;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;

/* @var \yii\web\View $this */
/* @var HeatMapLeadSearch $model */
?>

<div class="heat-map-lead-search">

    <?php $form = ActiveForm::begin([
        'id' => 'heatMapAgentForm',
        'action' => ['index'],
        'options' => [],
        'method' => 'get',
        'enableClientValidation' => true,
    ]) ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'dateRange', [
                'options' => ['class' => 'form-group'],
            ])->widget(\kartik\daterange\DateRangePicker::class, [
                'presetDropdown' => true,
                'hideInput' => true,
                'convertFormat' => true,
                'pluginOptions' => [
                    'dateLimit' => [
                        'days' => $model->getIntervalDaysDefault(),
                    ],
                    //'minDate' => $model->getFromDefaultDT(),
                    'maxDate' => $model->getToDefaultDT(),
                    'timePicker' => true,
                    'timePickerIncrement' => 1,
                    'timePicker24Hour' => true,
                    'locale' => [
                        'format' => 'Y-m-d H:i',
                        'separator' => ' - '
                    ],
                    'ranges' => [
                        "This week" => ["moment().startOf('isoWeek')", "moment().endOf('day')"],
                        "Last week" => ["moment().subtract(1, 'weeks').startOf('isoWeek')", "moment().subtract(1, 'weeks').endOf('isoWeek')"],
                        "This Month" => ["moment().startOf('month')", "moment().endOf('month')"],
                        "Last Month" => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                    ]
                ]
            ])->label('From / To') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'shifts', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => $shifts,
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select Shift', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Shift') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'department', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => $departments,
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select Department', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Department') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'userGroup', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => $userGroups,
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select User Group', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('User Group') ?>
        </div>

        <div class="col-md-2">
            <?php echo $form->field($model, 'roles')
                ->widget(Select2::class, [
                    'data' => $roles,
                    'size' => Select2::SMALL,
                    'options' => ['placeholder' => 'Select Role'],
                    'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                ]); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'timeZone')->widget(\kartik\select2\Select2::class, [
                'data' => Employee::timezoneList(true),
                'size' => \kartik\select2\Select2::SMALL,
                'options' => [
                    'placeholder' => 'Select TimeZone',
                    'multiple' => false,
                    'value' =>  $model->timeZone,
                ],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton('Generate report', ['class' => 'btn btn-primary js-search-btn']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['index?cache=-1'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
   </div>

   <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$(document).on('beforeSubmit', '#heatMapAgentForm', function(event) {
    let btn = $(this).find('.js-search-btn');
    btn.html('<i class="fa fa-cog fa-spin"></i> Loading').prop("disabled", true);
});
JS;
$this->registerJs($js, View::POS_READY);
