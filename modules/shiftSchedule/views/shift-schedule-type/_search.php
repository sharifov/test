<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleType\search\ShiftScheduleTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-schedule-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'sst_id') ?>

    <?= $form->field($model, 'sst_key') ?>

    <?= $form->field($model, 'sst_name') ?>

    <?= $form->field($model, 'sst_title') ?>

    <?= $form->field($model, 'sst_enabled') ?>

    <?php // echo $form->field($model, 'sst_readonly') ?>

    <?php // echo $form->field($model, 'sst_work_time') ?>

    <?php // echo $form->field($model, 'sst_color') ?>

    <?php // echo $form->field($model, 'sst_icon_class') ?>

    <?php // echo $form->field($model, 'sst_css_class') ?>

    <?php // echo $form->field($model, 'sst_params_json') ?>

    <?php // echo $form->field($model, 'sst_sort_order') ?>

    <?php // echo $form->field($model, 'sst_updated_dt') ?>

    <?php // echo $form->field($model, 'sst_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
