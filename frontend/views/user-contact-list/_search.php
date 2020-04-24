<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\UserContactListSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-contact-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ucl_user_id') ?>

    <?= $form->field($model, 'ucl_client_id') ?>

    <?= $form->field($model, 'ucl_title') ?>

    <?= $form->field($model, 'ucl_description') ?>

    <?= $form->field($model, 'ucl_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
