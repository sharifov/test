<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponUserAction\CouponUserActionSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="coupon-user-action-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cuu_id') ?>

    <?= $form->field($model, 'cuu_coupon_id') ?>

    <?= $form->field($model, 'cuu_action_id') ?>

    <?= $form->field($model, 'cuu_api_user_id') ?>

    <?= $form->field($model, 'cuu_user_id') ?>

    <?php // echo $form->field($model, 'cuu_description') ?>

    <?php // echo $form->field($model, 'cuu_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
