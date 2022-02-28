<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadStatusReason\entity\LeadStatusReasonSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-status-reason-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lsr_id') ?>

    <?= $form->field($model, 'lsr_key') ?>

    <?= $form->field($model, 'lsr_name') ?>

    <?= $form->field($model, 'lsr_description') ?>

    <?= $form->field($model, 'lsr_enabled') ?>

    <?php // echo $form->field($model, 'lsr_comment_required') ?>

    <?php // echo $form->field($model, 'lsr_params') ?>

    <?php // echo $form->field($model, 'lsr_created_user_id') ?>

    <?php // echo $form->field($model, 'lsr_updated_user_id') ?>

    <?php // echo $form->field($model, 'lsr_created_dt') ?>

    <?php // echo $form->field($model, 'lsr_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
