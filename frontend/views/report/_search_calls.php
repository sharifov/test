<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use src\access\EmployeeDepartmentAccess;
use common\models\Employee;

/**
 * @var $this yii\web\View
 * @var $model \src\model\callLog\entity\callLog\search\CallLogSearch;
 * @var $list
 */
?>

<div class="calls-search">
    <?php $form = ActiveForm::begin([
        'action' => ['report/calls-report'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'reportCreateTimeRange', [
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'presetDropdown' => false,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            //'minDate' => date("Y-m-d 00:00", strtotime("- 61 days")),
                            //'maxDate' => date("Y-m-d 23:59"),
                            'timePicker' => true,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'Y-m-d H:i',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Call Log Created Date');
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'reportTimezone')->widget(\kartik\select2\Select2::class, [
                'data' => Employee::timezoneList(true),
                'size' => \kartik\select2\Select2::SMALL,
                'options' => [
                    'placeholder' => 'Select TimeZone',
                    'multiple' => false,
                    'value' => $model->defaultUserTz
                ],
                'pluginOptions' => ['allowClear' => true],
            ]);
?>
        </div>
        <div class="col-md-3">
            <?php
            $hoursList = [];
            foreach (range(0, 23) as $hour) {
                $hoursList[sprintf("%02d:00", $hour)] = sprintf("%02d", $hour);
            }
            ?>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'timeFrom')->dropDownList($hoursList)->label('Hour From') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'timeTo')->dropDownList($hoursList, ['prompt' => ""])->label('Hour To') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'cl_user_id')->dropDownList($list->getEmployees(), ['prompt' => '-'])->label('Username') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'callDepId')->dropDownList(EmployeeDepartmentAccess::getDepartments(), ['prompt' => '-'])->label('Department') ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'cl_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-'])->label('Project') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'userGroupId')->dropDownList(\common\models\UserGroup::getList(), ['prompt' => '-'])->label('User Group') ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'minTalkTime')->input('number', ['min' => 0])->label(' Complete call Min talk time') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'maxTalkTime')->input('number', ['min' => 0])->label('Complete call Max talk time') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['name' => 'search', 'class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['report/calls-report'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>