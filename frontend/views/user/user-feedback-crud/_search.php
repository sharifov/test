<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\search\UserFeedbackSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-feedback-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'uf_id') ?>

    <?= $form->field($model, 'uf_type_id') ?>

    <?= $form->field($model, 'uf_status_id') ?>

    <?= $form->field($model, 'uf_title') ?>

    <?= $form->field($model, 'uf_message') ?>

    <?php // echo $form->field($model, 'uf_data_json') ?>

    <?php // echo $form->field($model, 'uf_created_dt') ?>

    <?php // echo $form->field($model, 'uf_updated_dt') ?>

    <?php // echo $form->field($model, 'uf_created_user_id') ?>

    <?php // echo $form->field($model, 'uf_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
