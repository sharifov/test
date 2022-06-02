<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\taskList\search\TaskListSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'tl_id') ?>

    <?= $form->field($model, 'tl_title') ?>

    <?= $form->field($model, 'tl_object') ?>

    <?= $form->field($model, 'tl_condition') ?>

    <?= $form->field($model, 'tl_condition_json') ?>

    <?php // echo $form->field($model, 'tl_params_json') ?>

    <?php // echo $form->field($model, 'tl_work_start_time_utc') ?>

    <?php // echo $form->field($model, 'tl_work_end_time_utc') ?>

    <?php // echo $form->field($model, 'tl_duration_min') ?>

    <?php // echo $form->field($model, 'tl_enable_type') ?>

    <?php // echo $form->field($model, 'tl_cron_expression') ?>

    <?php // echo $form->field($model, 'tl_sort_order') ?>

    <?php // echo $form->field($model, 'tl_updated_dt') ?>

    <?php // echo $form->field($model, 'tl_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
