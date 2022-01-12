<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponUse\CouponUse */
/* @var $form ActiveForm */
?>

<div class="coupon-use-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cu_coupon_id')->textInput() ?>

        <?= $form->field($model, 'cu_ip')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cu_user_agent')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
