<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\search\UserFeedbackFileSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-feedback-file-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'uff_id') ?>

    <?= $form->field($model, 'uff_uf_id') ?>

    <?= $form->field($model, 'uff_mimetype') ?>

    <?= $form->field($model, 'uff_size') ?>

    <?= $form->field($model, 'uff_filename') ?>

    <?php // echo $form->field($model, 'uff_title') ?>

    <?php // echo $form->field($model, 'uff_blob') ?>

    <?php // echo $form->field($model, 'uff_created_dt') ?>

    <?php // echo $form->field($model, 'uff_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
