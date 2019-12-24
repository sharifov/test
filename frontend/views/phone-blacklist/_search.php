<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\PhoneBlacklistSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="phone-blacklist-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pbl_id') ?>

    <?= $form->field($model, 'pbl_phone') ?>

    <?= $form->field($model, 'pbl_description') ?>

    <?= $form->field($model, 'pbl_enabled') ?>

    <?= $form->field($model, 'pbl_created_dt') ?>

    <?php // echo $form->field($model, 'pbl_updated_dt') ?>

    <?php // echo $form->field($model, 'pbl_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
