<?php

use src\model\coupon\entity\couponUserAction\CouponUserAction;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponUserAction\CouponUserAction */
/* @var $form ActiveForm */
?>

<div class="coupon-user-action-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cuu_coupon_id')->textInput() ?>

        <?= $form->field($model, 'cuu_action_id')->dropDownList(CouponUserAction::ACTION_LIST) ?>

        <?= $form->field($model, 'cuu_api_user_id', ['enableClientValidation' => false])->textInput() ?>

        <?= $form->field($model, 'cuu_user_id', ['enableClientValidation' => false])->textInput() ?>

        <?= $form->field($model, 'cuu_description')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
