<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\coupon\search\CouponSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="coupon-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'c_id') ?>

    <?= $form->field($model, 'c_code') ?>

    <?= $form->field($model, 'c_amount') ?>

    <?= $form->field($model, 'c_currency_code') ?>

    <?= $form->field($model, 'c_percent') ?>

    <?php // echo $form->field($model, 'c_exp_date') ?>

    <?php // echo $form->field($model, 'c_start_date') ?>

    <?php // echo $form->field($model, 'c_reusable') ?>

    <?php // echo $form->field($model, 'c_reusable_count') ?>

    <?php // echo $form->field($model, 'c_public') ?>

    <?php // echo $form->field($model, 'c_status_id') ?>

    <?php // echo $form->field($model, 'c_used_dt') ?>

    <?php // echo $form->field($model, 'c_disabled') ?>

    <?php // echo $form->field($model, 'c_type_id') ?>

    <?php // echo $form->field($model, 'c_created_dt') ?>

    <?php // echo $form->field($model, 'c_updated_dt') ?>

    <?php // echo $form->field($model, 'c_created_user_id') ?>

    <?php // echo $form->field($model, 'c_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
