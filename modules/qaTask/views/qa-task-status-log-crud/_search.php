<?php

use modules\qaTask\src\entities\qaTaskStatusLog\search\QaTaskStatusLogCrudSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model QaTaskStatusLogCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-task-status-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'tsl_id') ?>

    <?= $form->field($model, 'tsl_task_id') ?>

    <?= $form->field($model, 'tsl_start_status_id') ?>

    <?= $form->field($model, 'tsl_end_status_id') ?>

    <?= $form->field($model, 'tsl_start_dt') ?>

    <?php // echo $form->field($model, 'tsl_end_dt') ?>

    <?php // echo $form->field($model, 'tsl_duration') ?>

    <?php // echo $form->field($model, 'tsl_description') ?>

    <?php // echo $form->field($model, 'tsl_assigned_user_id') ?>

    <?php // echo $form->field($model, 'tsl_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
