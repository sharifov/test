<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use common\models\Lead;
use common\models\Employee;
use sales\auth\Auth;
?>

<div class="calls-search">
    <?php $form = ActiveForm::begin([
        'action' => ['stats/leads-stats'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'createTimeRange', [
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'presetDropdown' => false,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'minDate' => date("Y-m-d 00:00", strtotime("- 6 days")),
                            'maxDate' => date("Y-m-d 23:59"),
                            'timePicker' => true,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'Y-m-d H:i',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Created Date');
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
            foreach(range(0, 23) as $hour) {
                $hoursList[sprintf("%02d:00", $hour )] = sprintf("%02d", $hour );
            }
            ?>
            <!--<div class="row">
                <div class="col-md-3">
                    <?php /*= $form->field($model, 'timeFrom')->dropDownList($hoursList)->label('Hour From') */?>
                </div>
                <div class="col-md-3">
                    <?php /*= $form->field($model, 'timeTo')->dropDownList($hoursList, ['prompt' => ""])->label('Hour To') */?>
                </div>
            </div>-->
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'lfOwnerId')->dropDownList(Employee::getActiveUsersListFromCommonGroups(Auth::id()), ['prompt' => '-'])->label('User') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'departmentId')->dropDownList(EmployeeDepartmentAccess::getDepartments(Auth::id(), null), ['prompt' => '-'])->label('User Department') ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'userGroupId')->dropDownList(Auth::user()->getUserGroupList(), ['prompt' => '-'])->label('User Group') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'projectId')->dropDownList(EmployeeProjectAccess::getProjects(Auth::id(), null), ['prompt' => '-'])->label('Project') ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'createdType')->dropDownList(Lead::TYPE_CREATE_LIST, ['prompt' => '-'])->label('Lead Created Type') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['name' => 'search', 'class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['report/leads-stats'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

