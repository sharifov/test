<?php

use yii\widgets\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule */
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
                'action' => ['summary-report'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'startDateRange', [
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
                    ])->label('Start DateTime Range') ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Generate', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Reset', ['shift-schedule/summary-report'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>