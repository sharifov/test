<?php

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

        <?= $form->field($model, 'c_exp_date')->textInput() ?>

        <?= $form->field($model, 'c_start_date')->textInput() ?>

        <?= $form->field($model, 'c_reusable')->textInput() ?>

        <?= $form->field($model, 'c_reusable_count')->textInput() ?>

        <?= $form->field($model, 'c_public')->textInput() ?>

        <?= $form->field($model, 'c_status_id')->textInput() ?>

        <?= $form->field($model, 'c_used_dt')->textInput() ?>

        <?= $form->field($model, 'c_disabled')->textInput() ?>

        <?= $form->field($model, 'c_type_id')->textInput() ?>

        <?= $form->field($model, 'c_created_dt')->textInput() ?>

        <?= $form->field($model, 'c_updated_dt')->textInput() ?>

        <?= $form->field($model, 'c_created_user_id')->textInput() ?>

        <?= $form->field($model, 'c_updated_user_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
