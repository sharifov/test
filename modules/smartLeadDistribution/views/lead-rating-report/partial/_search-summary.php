<?php

use common\models\Department;
use common\models\UserGroup;
use kartik\select2\Select2;
use modules\smartLeadDistribution\src\SmartLeadDistribution;
use yii\widgets\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \modules\smartLeadDistribution\src\entities\LeadRatingReportSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>
<div class="user-shift-schedule-search">
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Generate options</h2>

            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <?php $form = ActiveForm::begin([
                'id' => 'search-form',
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'createdDateRange', [
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'presetDropdown' => true,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'timePicker' => true,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'Y-m-d H:i:s',
                                'separator' => ' - '
                            ],
                            'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                        ]
                    ])->label('Created DateTime Range') ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'ratingCategoryIds')
                        ->widget(Select2::class, [
                            'data' => SmartLeadDistribution::CATEGORY_LIST,
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select Category'],
                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                        ]); ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'leadStatusIds')
                        ->widget(Select2::class, [
                            'data' => \common\models\Lead::STATUS_LIST,
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select Status'],
                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                        ]); ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'projectIds')
                        ->widget(Select2::class, [
                            'data' => \common\models\Project::getList(),
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select Project'],
                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                        ]); ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'userGroupIds')
                        ->widget(Select2::class, [
                            'data' => Yii::$app->user->identity->isAdmin() ? UserGroup::getList() : Yii::$app->user->identity->getUserGroupList(),
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select Project'],
                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                        ]); ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'userDepartmentIds')
                        ->widget(Select2::class, [
                            'data' => Department::getList(),
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select Project'],
                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                        ]); ?>
                </div>


            </div>

            <div class="form-group">
                <?= Html::submitButton('Generate', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>