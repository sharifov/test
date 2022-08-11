<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTaskStatusLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-task-status-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'utsl_id') ?>

    <?= $form->field($model, 'utsl_ut_id') ?>

    <?= $form->field($model, 'utsl_description') ?>

    <?= $form->field($model, 'utsl_old_status') ?>

    <?= $form->field($model, 'utsl_new_status') ?>

    <?php // echo $form->field($model, 'utsl_created_user_id') ?>

    <?php // echo $form->field($model, 'utsl_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
