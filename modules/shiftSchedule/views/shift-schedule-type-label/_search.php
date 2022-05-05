<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\search\ShiftScheduleTypeLabelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-schedule-type-label-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'stl_key') ?>

    <?= $form->field($model, 'stl_name') ?>

    <?= $form->field($model, 'stl_enabled') ?>

    <?= $form->field($model, 'stl_color') ?>

    <?= $form->field($model, 'stl_icon_class') ?>

    <?php // echo $form->field($model, 'stl_params_json') ?>

    <?php // echo $form->field($model, 'stl_sort_order') ?>

    <?php // echo $form->field($model, 'stl_updated_dt') ?>

    <?php // echo $form->field($model, 'stl_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
