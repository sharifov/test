<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponClient\CouponClientSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="coupon-client-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cuc_id') ?>

    <?= $form->field($model, 'cuc_coupon_id') ?>

    <?= $form->field($model, 'cuc_client_id') ?>

    <?= $form->field($model, 'cuc_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
