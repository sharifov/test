<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use sales\access\EmployeeDepartmentAccess;
use common\models\Employee;
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
                    <?= $form->field($model, 'createTimeRange', [
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'presetDropdown' => false,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'timePicker' => false,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'Y-m-d',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Calls Created Date');
                    ?>
                </div>

                <!--<div class="col-md-12">
                    <div class="row">

                </div>-->

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

    <dinv class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'c_created_user_id')->dropDownList($list->getEmployees(), ['prompt' => '-'])->label('Username') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'callDepId')->dropDownList(EmployeeDepartmentAccess::getDepartments(), ['prompt' => '-'])->label('Department') ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'c_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-'])->label('Project') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'userGroupId')->dropDownList(\common\models\UserGroup::getList(), ['prompt' => '-'])->label('User Group') ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'call_duration_from')->input('number', ['min' => 0]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'call_duration_to')->input('number', ['min' => 0]) ?>
                </div>
            </div>
        </div>
    </dinv>

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