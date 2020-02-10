<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTask\search\QaTaskCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 't_id') ?>

    <?= $form->field($model, 't_gid') ?>

    <?= $form->field($model, 't_object_type_id') ?>

    <?= $form->field($model, 't_object_id') ?>

    <?= $form->field($model, 't_category_id') ?>

    <?php // echo $form->field($model, 't_status_id') ?>

    <?php // echo $form->field($model, 't_rating') ?>

    <?php // echo $form->field($model, 't_create_type_id') ?>

    <?php // echo $form->field($model, 't_description') ?>

    <?php // echo $form->field($model, 't_department_id') ?>

    <?php // echo $form->field($model, 't_deadline_dt') ?>

    <?php // echo $form->field($model, 't_assigned_user_id') ?>

    <?php // echo $form->field($model, 't_created_user_id') ?>

    <?php // echo $form->field($model, 't_updated_user_id') ?>

    <?php // echo $form->field($model, 't_created_dt') ?>

    <?php // echo $form->field($model, 't_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
