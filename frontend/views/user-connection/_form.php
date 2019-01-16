<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserConnection */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-connection-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'uc_connection_id')->textInput() ?>

    <?= $form->field($model, 'uc_user_id')->textInput() ?>

    <?= $form->field($model, 'uc_lead_id')->textInput() ?>

    <?= $form->field($model, 'uc_user_agent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uc_controller_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uc_action_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uc_page_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uc_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uc_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
