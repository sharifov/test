<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserOnline */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-online-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'uo_user_id')->textInput() ?>

    <?= $form->field($model, 'uo_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
