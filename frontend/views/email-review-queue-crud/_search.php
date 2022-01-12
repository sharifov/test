<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\emailReviewQueue\entity\EmailReviewQueueSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-review-queue-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'erq_id') ?>

    <?= $form->field($model, 'erq_email_id') ?>

    <?= $form->field($model, 'erq_project_id') ?>

    <?= $form->field($model, 'erq_department_id') ?>

    <?= $form->field($model, 'erq_owner_id') ?>

    <?php // echo $form->field($model, 'erq_status_id') ?>

    <?php // echo $form->field($model, 'erq_user_reviewer_id') ?>

    <?php // echo $form->field($model, 'erq_created_dt') ?>

    <?php // echo $form->field($model, 'erq_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
