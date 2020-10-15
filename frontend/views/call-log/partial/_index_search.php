<?php

use yii\helpers\Html;
use \kartik\form\ActiveForm;
use sales\widgets\UserSelect2Widget;
use kartik\select2\Select2;
use common\models\Project;
use common\models\UserGroup;
use common\models\Department;

/* @var $this yii\web\View */
/* @var $model common\models\search\CallSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-search">
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>

                <?php /*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-comment"></i></a>


                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>*/?>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: ">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>


            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'createTimeRange', [
                        //'addon'=>['prepend'=>['content'=>'<i class="fa fa-calendar"></i>']],
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'useWithAddon' => true,
                        'presetDropdown' => true,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'startAttribute' => 'createTimeStart',
                        'endAttribute' => 'createTimeEnd',
                        'pluginOptions' => [
                            'timePicker' => true,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'Y-m-d H:i',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Created DateTime Range');
                    ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'userID')->widget(UserSelect2Widget::class, [
                        'data' => $model->userID ? [
                            $model->userID => \common\models\Employee::findOne($model->userID)->username
                        ] : [],
                    ])->label('User') ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'projectIds', [
                        'options' => ['class' => 'form-group']
                    ])->widget(Select2::class, [
                        'data' => Project::getList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select Project', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label('Project') ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'departmentIds', [
                        'options' => ['class' => 'form-group']
                    ])->widget(Select2::class, [
                        'data' => Department::getList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select Department', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label('Department') ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'userGroupIds', [
                        'options' => ['class' => 'form-group']
                    ])->widget(Select2::class, [
                        'data' => UserGroup::getList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select User Group', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label('User Groups') ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'minTalkTime')->input('number', ['min' => 0])->label('Talk time from') ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'maxTalkTime')->input('number', ['min' => 0])->label('Talk time to') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary']) ?>
                        <?= Html::resetButton('<i class="fa fa-close"></i> Reset form', ['class' => 'btn btn-default']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
