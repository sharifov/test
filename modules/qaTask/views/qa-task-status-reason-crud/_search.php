<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskStatusReason\search\QaTaskStatusReasonCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-task-status-reason-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'tsr_id') ?>

    <?= $form->field($model, 'tsr_object_type_id') ?>

    <?= $form->field($model, 'tsr_status_id') ?>

    <?= $form->field($model, 'tsr_key') ?>

    <?= $form->field($model, 'tsr_name') ?>

    <?php // echo $form->field($model, 'tsr_description') ?>

    <?php // echo $form->field($model, 'tsr_comment_required') ?>

    <?php // echo $form->field($model, 'tsr_enabled') ?>

    <?php // echo $form->field($model, 'tsr_created_user_id') ?>

    <?php // echo $form->field($model, 'tsr_updated_user_id') ?>

    <?php // echo $form->field($model, 'tsr_created_dt') ?>

    <?php // echo $form->field($model, 'tsr_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
