<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserCallStatus */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-call-status-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'us_type_id')->textInput() ?>

    <?= $form->field($model, 'us_user_id')->textInput() ?>

    <?= $form->field($model, 'us_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
