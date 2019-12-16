<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use sales\access\EmployeeDepartmentAccess;
use common\models\Lead;
use common\models\Employee;
?>

<div class="calls-search">
    <?php $form = ActiveForm::begin([
        'action' => ['report/leads-report'],
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
                            'minDate' => date("Y-m-d", strtotime("- 61 days")),
                            'maxDate' => date("Y-m-d"),
                            'timePicker' => false,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'Y-m-d',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Created Date');
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="col-md-6">
                <?=
                $form->field($model, 'timeFrom')->widget(
                    \kartik\time\TimePicker::class, [
                    'pluginOptions' => [
                        'defaultTime' => '00:00',
                        'showSeconds' => false,
                        'showMeridian' => false,
                    ]])->label('Report Hour From');
                ?>
            </div>
            <div class="col-md-6">
                <?=
                $form->field($model, 'timeTo')->widget(
                    \kartik\time\TimePicker::class, [
                    'pluginOptions' => [
                        'defaultTime' => '23:59',
                        'showSeconds' => false,
                        'showMeridian' => false,
                    ]])->label('Report Hour To');
                ?>
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
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'lfOwnerId')->dropDownList($list->getEmployees(), ['prompt' => '-'])->label('User') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'departmentId')->dropDownList(EmployeeDepartmentAccess::getDepartments(), ['prompt' => '-'])->label('User Department') ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'userGroupId')->dropDownList(\common\models\UserGroup::getList(), ['prompt' => '-'])->label('User Group') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'projectId')->dropDownList(\common\models\Project::getList(), ['prompt' => '-'])->label('Project') ?>
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
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['report/leads-report'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
