<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\authClient\entity\AuthClientSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-client-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ac_id') ?>

    <?= $form->field($model, 'ac_user_id') ?>

    <?= $form->field($model, 'ac_source') ?>

    <?= $form->field($model, 'ac_source_id') ?>

    <?= $form->field($model, 'ac_email') ?>

    <?php // echo $form->field($model, 'ac_ip') ?>

    <?php // echo $form->field($model, 'ac_useragent') ?>

    <?php // echo $form->field($model, 'ac_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
