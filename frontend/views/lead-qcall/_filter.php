<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadQcallSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-qcall-filter">

    <?php $form = ActiveForm::begin([
        'action' => ['list'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

        <div class="col-md-2">
            <?//= $form->field($model, 'current_dt') ?>

            <?= $form->field($model, 'current_dt')->widget(DateTimePicker::class, [
                'language' => 'en',
                'size' => 'ms',
                //'template' => '{input}',
                'pickButtonIcon' => 'glyphicon glyphicon-time',
                //'inline' => true,
                'clientOptions' => [
                    //'startView' => 1,
                    //'minView' => 0,
                    //'maxView' => 1,
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii',
                    'linkFormat' => 'HH:ii P', // if inline = true
                    // 'format' => 'HH:ii P', // if inline = false
                    'todayBtn' => true
                ]
            ])->label('Current Date Time')?>

        </div>
        <div class="col-md-12 text-center">
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="fa fa-refresh"></i> Refresh', ['lead-qcall/list'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>
