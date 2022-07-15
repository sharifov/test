<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\clientUserReturn\entity\ClientUserReturn */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-user-return-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cur_client_id')->textInput() ?>

    <?= $form->field($model, 'cur_user_id')->textInput() ?>

    <?= $form->field($model, 'cur_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
