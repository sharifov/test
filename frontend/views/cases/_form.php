<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\Cases */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cases-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cs_subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cs_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'cs_category')->textInput() ?>

    <?= $form->field($model, 'cs_status')->textInput() ?>

    <?= $form->field($model, 'cs_user_id')->textInput() ?>

    <?= $form->field($model, 'cs_lead_id')->textInput() ?>

    <?= $form->field($model, 'cs_call_id')->textInput() ?>

    <?= $form->field($model, 'cs_dep_id')->textInput() ?>

    <?= $form->field($model, 'cs_client_id')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
