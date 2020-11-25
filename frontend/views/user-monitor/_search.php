<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\monitor\search\UserMonitorSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-monitor-search">
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: <?=(/*Yii::$app->request->isPjax ||*/ Yii::$app->request->get('UserMonitorSearch') || Yii::$app->request->get('createTimeRange')) ? 'block' : 'none'?>">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <div class="row">
                <div class="col-md-1">
                    <?= $form->field($model, 'um_id') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'userId')->dropDownList(\common\models\Employee::getList(), ['prompt' => '']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'typeId')->dropDownList(\sales\model\user\entity\monitor\UserMonitor::getTypeList(), ['prompt' => '']) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'timeRange', [
                                'options' => ['class' => 'form-group']
                            ])->widget(\kartik\daterange\DateRangePicker::class, [
                                'presetDropdown' => false,
                                'hideInput' => true,
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'timePicker' => true,
                                    'timePickerIncrement' => 1,
                                    'timePicker24Hour' => true,
                                    'locale' => [
                                        'format' => 'Y-m-d H:i',
                                        'separator' => ' - '
                                    ]
                                ],
                                'startAttribute' => 'startTime',
                                'endAttribute' => 'endTime',
                            ])->label('User Monitor Start/End Date');
?>
                </div>

                <!--<div class="col-md-1">
                    <?php // echo $form->field($model, 'um_period_sec')->label('Period Sec') ?>
                </div>

                <div class="col-md-3">
                    <?php // echo $form->field($model, 'um_description')->label('Description') ?>
                </div>-->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset', ['user-monitor/index'], ['class' => 'btn btn-warning']) ?>
                        <?php // Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
