<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model \sales\model\user\entity\monitor\search\UserMonitorSearch */
?>

<div class="user-monitor-stats-search">
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
        <div class="x_content" style="display: none">

            <?php $form = ActiveForm::begin([
                'method' => 'get',
            ]); ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-12">
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
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <br>
                    <div class="form-group text-center">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['name' => 'search', 'class' => 'btn btn-primary']) ?>
                        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset', ['user-monitor/stats'], ['class' => 'btn btn-warning']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>