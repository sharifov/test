<?php

use sales\model\coupon\entity\coupon\CouponStatus;
use sales\model\coupon\entity\coupon\CouponType;
use sales\widgets\DateTimePicker;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\coupon\Coupon */
/* @var $form ActiveForm */
?>

<div class="coupon-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'c_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'c_amount')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'c_currency_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'c_percent')->textInput() ?>

        <?= $form->field($model, 'c_exp_date')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'c_start_date')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'c_reusable')->checkbox() ?>

        <?= $form->field($model, 'c_reusable_count')->textInput() ?>

        <?= $form->field($model, 'c_public')->checkbox() ?>

        <?= $form->field($model, 'c_status_id')->dropDownList(CouponStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'c_used_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'c_disabled')->checkbox() ?>

        <?= $form->field($model, 'c_type_id')->dropDownList(CouponType::getList()) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
