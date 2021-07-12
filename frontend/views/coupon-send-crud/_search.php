<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponSend\CouponSendSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="coupon-send-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cus_id') ?>

    <?= $form->field($model, 'cus_coupon_id') ?>

    <?= $form->field($model, 'cus_user_id') ?>

    <?= $form->field($model, 'cus_type_id') ?>

    <?= $form->field($model, 'cus_send_to') ?>

    <?php // echo $form->field($model, 'cus_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
