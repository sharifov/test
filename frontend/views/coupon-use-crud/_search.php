<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponUse\CouponUseSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="coupon-use-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cu_id') ?>

    <?= $form->field($model, 'cu_coupon_id') ?>

    <?= $form->field($model, 'cu_ip') ?>

    <?= $form->field($model, 'cu_user_agent') ?>

    <?= $form->field($model, 'cu_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
