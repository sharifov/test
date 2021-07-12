<?php

use sales\model\coupon\entity\couponSend\CouponSend;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponSend\CouponSend */
/* @var $form ActiveForm */
?>

<div class="coupon-send-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cus_coupon_id')->textInput() ?>

        <?= $form->field($model, 'cus_user_id')->textInput() ?>

        <?= $form->field($model, 'cus_type_id')->dropDownList(CouponSend::TYPE_LIST) ?>

        <?= $form->field($model, 'cus_send_to')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
