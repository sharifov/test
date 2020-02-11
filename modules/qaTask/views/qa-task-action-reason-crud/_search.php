<?php

use modules\qaTask\src\entities\qaTaskActionReason\search\QaTaskActionReasonCrudSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model QaTaskActionReasonCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-task-action-reason-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'tar_id') ?>

    <?= $form->field($model, 'tar_object_type_id') ?>

    <?= $form->field($model, 'tar_action_id') ?>

    <?= $form->field($model, 'tar_key') ?>

    <?= $form->field($model, 'tar_name') ?>

    <?php // echo $form->field($model, 'tar_description') ?>

    <?php // echo $form->field($model, 'tar_comment_required') ?>

    <?php // echo $form->field($model, 'tar_enabled') ?>

    <?php // echo $form->field($model, 'tar_created_user_id') ?>

    <?php // echo $form->field($model, 'tar_updated_user_id') ?>

    <?php // echo $form->field($model, 'tar_created_dt') ?>

    <?php // echo $form->field($model, 'tar_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
