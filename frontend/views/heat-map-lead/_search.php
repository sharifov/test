<?php

use src\model\lead\reports\HeatMapLeadSearch;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var \yii\web\View $this */
/* @var HeatMapLeadSearch $model */

/* TODO::  */
?>
<div class="heat-map-lead-search">

    <?php $form = ActiveForm::begin([
        'id' => 'UserStatsReportForm',
        'action' => ['index'],
        'options' => [
          //  'data-pjax' => 1
        ],
        'method' => 'get',
        'enableClientValidation' => true,
    ]) ?>

    <hr />
    <div class="row">

        <div class="col-md-3">
            <?= $form->field($model, 'dateRange', [
                'options' => ['class' => 'form-group'],
            ])->widget(\kartik\daterange\DateRangePicker::class, [
                'presetDropdown' => true,
                'hideInput' => true,
                'convertFormat' => true,
                'pluginOptions' => [
                    'minDate' => "2018-01-01 00:00", /* TODO::  */
                    'maxDate' => date("Y-m-d 23:59"),
                    'timePicker' => true,
                    'timePickerIncrement' => 1,
                    'timePicker24Hour' => true,
                    'locale' => [
                        'format' => 'Y-m-d H:i',
                        'separator' => ' - '
                    ],
                    'ranges' => [
                        "This week" => ["moment().startOf('isoWeek')", "moment().endOf('day')"],
                        "Last week" => ["moment().subtract(1, 'weeks').startOf('isoWeek')", "moment().subtract(1, 'weeks').endOf('isoWeek')"],
                        "This Month" => ["moment().startOf('month')", "moment().endOf('month')"],
                        "Last Month" => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                    ]
                ]
            ])->label('From / To') ?>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton('Generate report', ['class' => 'btn btn-primary js-user-stats-btn']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['report?reset=1'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
   </div>

   <?php ActiveForm::end(); ?>
</div>