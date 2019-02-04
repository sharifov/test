<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ClientPhone */
/* @var $form yii\widgets\ActiveForm */

$dataClients = \common\models\Client::getList();
?>

<div class="client-phone-form">

    <?php $form = ActiveForm::begin(); ?>



    <? //= $form->field($model, 'client_id')->textInput() ?>

    <?php
    echo $form->field($model, 'client_id')->dropDownList($dataClients, ['prompt' => '---']);
    ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created')->textInput() ?>

    <?= $form->field($model, 'updated')->textInput() ?>

    <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'is_sms')->textInput() ?>

    <?= $form->field($model, 'validate_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
