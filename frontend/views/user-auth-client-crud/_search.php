<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\userAuthClient\entity\UserAuthClientSearch */
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

    <?= $form->field($model, 'uac_id') ?>

    <?= $form->field($model, 'uac_user_id') ?>

    <?= $form->field($model, 'uac_source') ?>

    <?= $form->field($model, 'uac_source_id') ?>

    <?= $form->field($model, 'uac_email') ?>

    <?php // echo $form->field($model, 'uac_ip') ?>

    <?php // echo $form->field($model, 'uac_useragent') ?>

    <?php // echo $form->field($model, 'uac_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
