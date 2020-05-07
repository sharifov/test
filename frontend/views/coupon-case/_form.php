<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponCase\CouponCase */
/* @var $form ActiveForm */
?>

<div class="coupon-case-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cc_coupon_id')->textInput() ?>

        <?= $form->field($model, 'cc_case_id')->textInput() ?>

        <?= $form->field($model, 'cc_sale_id')->textInput() ?>

        <?= $form->field($model, 'cc_created_dt')->textInput() ?>

        <?= $form->field($model, 'cc_created_user_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
