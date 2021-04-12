<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderEmail\OrderEmail */
/* @var $form ActiveForm */
?>

<div class="order-email-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'oe_order_id')->textInput() ?>

        <?= $form->field($model, 'oe_email_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
