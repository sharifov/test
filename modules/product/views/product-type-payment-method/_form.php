<?php

use common\models\Employee;
use common\models\PaymentMethod;
use dosamigos\datetimepicker\DateTimePicker;
use modules\product\src\entities\productType\ProductTypeQuery;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\ProductTypePaymentMethod\ProductTypePaymentMethod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-type-payment-method-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'ptpm_produt_type_id')->dropDownList(ProductTypeQuery::getListAll(), ['prompt' => '--']) ?>

            <?= $form->field($model, 'ptpm_payment_method_id')->dropDownList(PaymentMethod::getList(), ['prompt' => '--']) ?>

            <?= $form->field($model, 'ptpm_payment_fee_percent')->textInput(['type' => 'number', 'step' => '0.01']) ?>

            <?= $form->field($model, 'ptpm_payment_fee_amount')->textInput(['maxlength' => true, 'type' => 'number']) ?>

            <?= $form->field($model, 'ptpm_enabled')->checkbox() ?>

            <?= $form->field($model, 'ptpm_default')->checkbox() ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
