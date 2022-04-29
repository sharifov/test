<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\search\ShiftScheduleTypeLabelAssignSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-schedule-type-label-assign-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'tla_stl_key') ?>

    <?= $form->field($model, 'tla_sst_id') ?>

    <?= $form->field($model, 'tla_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
