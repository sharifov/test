<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\GlobalLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="global-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'gl_id') ?>

    <?= $form->field($model, 'gl_app_id') ?>

    <?= $form->field($model, 'gl_app_user_id') ?>

    <?= $form->field($model, 'gl_model') ?>

    <?= $form->field($model, 'gl_obj_id') ?>

    <?php // echo $form->field($model, 'gl_old_attr') ?>

    <?php // echo $form->field($model, 'gl_new_attr') ?>

    <?php // echo $form->field($model, 'gl_formatted_attr') ?>

    <?php // echo $form->field($model, 'gl_created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
