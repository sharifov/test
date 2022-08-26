<?php

use modules\user\userActivity\entity\search\UserActivitySearch;
use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserActivitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="case-category-search">
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <?php $form = ActiveForm::begin([
                'action' => ['dashboard'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'dateTimeRange', [
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'useWithAddon' => true,
                        'presetDropdown' => true,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'startAttribute' => 'timeStart',
                        'endAttribute' => 'timeEnd',
                        'pluginOptions' => [
                            'timePicker' => true,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'Y-m-d H:i',
                                'separator' => ' - '
                            ],
                            'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                        ]
                    ])->label('Created DateTime Range');
                    ?>
                </div>
                <div class="col-md-3">
                    <?php /*= $form->field($model, 'reportTimezone')->widget(\kartik\select2\Select2::class, [
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
                <div class="col-md-2">
                    <?php /*= $form->field($model, 'depID')->dropDownList(EmployeeDepartmentAccess::getDepartments(), ['prompt' => '-'])*/ ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <?= Html::submitButton('<i class="fa fa-filter"></i> Show data', ['class' => 'btn btn-primary']) ?>
                        <?php /* Html::resetButton('<i class="fa fa-close"></i> Reset form', ['class' => 'btn btn-default'])*/ ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
