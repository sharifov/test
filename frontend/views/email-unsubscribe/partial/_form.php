<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EmailUnsubscribe */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-unsubscribe-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'eu_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'eu_project_id')->textInput() ?>

    <?= $form->field($model, 'eu_created_user_id')->textInput() ?>

    <?= $form->field($model, 'eu_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
