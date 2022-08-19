<?php

use common\models\Employee;
use common\models\UserGroup;
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
        'id' => 'heatMapLeadForm',
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
            <?= $form->field($model, 'project', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => \common\models\Project::getList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select Project', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Project') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'department', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => \common\models\Department::getList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select Department', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Department') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'source', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => \common\models\Sources::getList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select Source', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Source') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'userGroup', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => UserGroup::getList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select User Group', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('User Group') ?>
        </div>

        <div class="col-md-2">
            <?php echo $form->field($model, 'employee')->widget(Select2::class, [
                'data' => Employee::getActiveUsersList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select user', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Owner'); ?>
        </div>

        <div class="col-md-2">
            <?php echo $form->field($model, 'typeCreate')->widget(Select2::class, [
                'data' => \common\models\Lead::TYPE_CREATE_LIST,
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select type', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Type Create'); ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'isAnswered')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '-'])->label() ?>
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
$(document).on('beforeSubmit', '#heatMapLeadForm', function(event) {
    let btn = $(this).find('.js-search-btn');
    btn.html('<i class="fa fa-cog fa-spin"></i> Loading').prop("disabled", true);
});
JS;
$this->registerJs($js, View::POS_READY);
