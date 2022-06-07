<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTaskSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="user-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ut_id') ?>

    <?= $form->field($model, 'ut_user_id') ?>

    <?= $form->field($model, 'ut_target_object') ?>

    <?= $form->field($model, 'ut_target_object_id') ?>

    <?= $form->field($model, 'ut_task_list_id') ?>

    <?php // echo $form->field($model, 'ut_start_dt') ?>

    <?php // echo $form->field($model, 'ut_end_dt') ?>

    <?php // echo $form->field($model, 'ut_priority') ?>

    <?php // echo $form->field($model, 'ut_status_id') ?>

    <?php // echo $form->field($model, 'ut_created_dt') ?>

    <?php // echo $form->field($model, 'ut_year') ?>

    <?php // echo $form->field($model, 'ut_month') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
