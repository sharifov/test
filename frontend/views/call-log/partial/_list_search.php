<?php

use common\models\Call;
use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use common\models\Department;
use common\models\Project;

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
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
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
        <div class="x_content" style="display: <?=(Yii::$app->request->isPjax || Yii::$app->request->get('CallLogSearch') || Yii::$app->request->get('createTimeRange')) ? 'block' : 'none'?>">
            <?php $form = ActiveForm::begin([
                'action' => ['list'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>


            <div class="row">
                <div class="col-md-3">
                    <?php echo $form->field($model, 'createTimeRange', [
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
                            ],
                            'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                        ],
                    ])->label('Created DateTime Range');
                    ?>
                </div>

                <div class="col-md-3">
                    <div class="col-md-6">
                        <?php echo $form->field($model, 'callDurationFrom')->input('number', ['min' => 0]) ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $form->field($model, 'callDurationTo')->input('number', ['min' => 0]) ?>
                    </div>
                </div>

                <?php if (count(Project::getList()) > 1) : ?>
                <div class="col-md-2">
                    <?= $form->field($model, 'projectIds', [
                        'options' => ['class' => 'form-group']
                    ])->widget(Select2::class, [
                        'data' => Project::getListByUser(Yii::$app->user->id),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select Project', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label('Project') ?>
                </div>
                <?php endif; ?>

                <div class="col-md-2">
                    <?= $form->field($model, 'statusIds', [
                        'options' => ['class' => 'form-group']
                    ])->widget(Select2::class, [
                        'data' => src\model\callLog\entity\callLog\CallLogStatus::getList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select Status', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label('Status') ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'typesIds', [
                        'options' => ['class' => 'form-group']
                    ])->widget(Select2::class, [
                        'data' => src\model\callLog\entity\callLog\CallLogType::getList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select Type', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label('Type') ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'categoryIds', [
                        'options' => ['class' => 'form-group']
                    ])->widget(Select2::class, [
                        'data' => Call::SOURCE_LIST,
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select Category', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label('Category') ?>
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
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('<i class="fa fa-close"></i> Reset form', ['list'], ['class' => 'btn btn-default']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

