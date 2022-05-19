<?php

use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use modules\shiftSchedule\src\entities\shift\Shift;
use src\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>
<div class="user-shift-schedule-search">
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
        <div class="x_content" style="display: <?=(Yii::$app->request->isPjax) ? 'block' : 'none'?>">
            <?php $form = ActiveForm::begin([
                'id' => 'search-form',
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <div class="row">
                <div class="col-md-1">
                    <?= $form->field($model, 'uss_id') ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'uss_user_id')->widget(UserSelect2Widget::class) ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'shiftIds', [
                        'options' => ['class' => 'form-group']
                    ])->widget(Select2::class, [
                        'data' => Shift::getList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select Shift', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label('Shifts') ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'startedDateRange', [
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
                                'format' => 'd-M-Y H:i',
                                'separator' => ' - '
                            ],
                            'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                        ]
                    ])->label('Started DateTime Range') ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'endedDateRange', [
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
                                'format' => 'd-M-Y H:i',
                                'separator' => ' - '
                            ],
                            'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                        ]
                    ])->label('Ended DateTime Range') ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'uss_duration') ?>
                </div>
            </div>

            <?php // echo $form->field($model, 'uss_ssr_id') ?>

            <?php // echo $form->field($model, 'uss_description') ?>

            <?php // echo $form->field($model, 'uss_start_utc_dt') ?>

            <?php // echo $form->field($model, 'uss_end_utc_dt') ?>

            <?php // echo $form->field($model, 'uss_duration') ?>

            <?php // echo $form->field($model, 'uss_status_id') ?>

            <?php // echo $form->field($model, 'uss_type_id') ?>

            <?php // echo $form->field($model, 'uss_customized') ?>

            <?php // echo $form->field($model, 'uss_created_dt') ?>

            <?php // echo $form->field($model, 'uss_updated_dt') ?>

            <?php // echo $form->field($model, 'uss_created_user_id') ?>

            <?php // echo $form->field($model, 'uss_updated_user_id') ?>

            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>