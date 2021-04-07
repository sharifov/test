<?php

use common\models\Payment;
use frontend\extensions\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Payment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pay_type_id')->textInput() ?>

    <?= $form->field($model, 'pay_method_id')->textInput() ?>

    <?= $form->field($model, 'pay_status_id')->dropDownList(Payment::getStatusList(), ['prompt' => '---']) ?>

    <?= $form->field($model, 'pay_code')->textInput() ?>

    <?= $form->field($model, 'pay_date')->widget(DatePicker::class, [
        'clientOptions' => [
            'format' => 'yyyy-mm-dd',
        ]
    ]) ?>

    <?= $form->field($model, 'pay_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pay_currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pay_invoice_id')->textInput() ?>

    <?= $form->field($model, 'pay_order_id')->textInput() ?>

    <?= $form->field($model, 'pay_description')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
