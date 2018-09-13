<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ApiLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'al_request_data')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'al_request_dt')->textInput() ?>

    <?= $form->field($model, 'al_response_data')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'al_response_dt')->textInput() ?>

    <?= $form->field($model, 'al_ip_address')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
