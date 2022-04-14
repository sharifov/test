<?php

use common\models\Employee;
use kartik\select2\Select2;
use src\model\user\reports\stats\UserStatsReport;
use src\model\userModelSetting\service\UserModelSettingDictionary;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var UserStatsReport $model */
/* @var yii\widgets\ActiveForm $form */
?>

<div class="user-stats-search">

    <?php $form = ActiveForm::begin([
        'id' => 'UserStatsReportForm',
        'action' => ['report'],
        'options' => [
          //  'data-pjax' => 1
        ],
        'method' => 'get',
        'enableClientValidation' => true,
    ]) ?>

    <hr />
    <div class="row">

        <div class="col-md-3">
            <?= $form->field($model, 'dateRange', [
                'options' => ['class' => 'form-group'],
            ])->widget(\kartik\daterange\DateRangePicker::class, [
                'presetDropdown' => true,
                'hideInput' => true,
                'convertFormat' => true,
                'pluginOptions' => [
                    'minDate' => "2018-01-01 00:00",
                    'maxDate' => date("Y-m-d 23:59"),
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

        <div class="col-md-2">
            <?= $form->field($model, 'groupBy', [
                'options' => ['class' => 'form-group']
            ])
                ->dropDownList($model->getGroupByList(), ['prompt' => '---'])
            ->label('Group By') ?>
        </div>

        <div class="col-md-5">
            <?= $form->field($model, 'metrics', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => $model->getMetricsList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select Metrics', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Metrics') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'departments', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => $model->getDepartmentList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select Department', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Department') ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'roles', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => $model->getRolesList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select Roles', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Roles') ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'groups', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => $model->getGroupList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select Groups', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Groups') ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'user', [
                'options' => ['class' => 'form-group']
            ])->widget(Select2::class, [
                'data' => $model->getUsersList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select User', 'multiple' => false],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Users') ?>
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
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton('Generate report', ['class' => 'btn btn-primary js-user-stats-btn']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['report?reset=1'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
