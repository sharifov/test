<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponClient\CouponClient */
/* @var $form ActiveForm */
?>

<div class="coupon-client-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cuc_coupon_id')->textInput() ?>

        <?= $form->field($model, 'cuc_client_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
