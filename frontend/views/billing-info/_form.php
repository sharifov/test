<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BillingInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="billing-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'bi_first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_middle_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_company_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_address_line1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_address_line2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_state')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_zip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_contact_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_contact_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_contact_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bi_payment_method_id')->textInput() ?>

    <?= $form->field($model, 'bi_cc_id')->textInput() ?>

    <?= $form->field($model, 'bi_order_id')->textInput() ?>

    <?= $form->field($model, 'bi_status_id')->textInput() ?>

    <?= $form->field($model, 'bi_created_user_id')->textInput() ?>

    <?= $form->field($model, 'bi_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'bi_created_dt')->textInput() ?>

    <?= $form->field($model, 'bi_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
