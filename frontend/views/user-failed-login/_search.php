<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\search\UserFailedLoginSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-failed-login-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ufl_id') ?>

    <?= $form->field($model, 'ufl_username') ?>

    <?= $form->field($model, 'ufl_user_id') ?>

    <?= $form->field($model, 'ufl_ua') ?>

    <?= $form->field($model, 'ufl_ip') ?>

    <?php // echo $form->field($model, 'ufl_session_id') ?>

    <?php // echo $form->field($model, 'ufl_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
