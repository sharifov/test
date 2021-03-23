<?php

use common\models\Payment;
use common\models\PaymentMethod;
use frontend\extensions\DatePicker;
use yii\helpers\Html;

/** @var $model Payment */

$pjaxId = 'pjax-payment-update-form-' . $model->pay_id;

?>
<div class="payment-form">

    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
    <?php \yii\widgets\Pjax::begin([
        'id' => $pjaxId,
        'timeout' => 5000,
        'enablePushState' => false,
        'enableReplaceState' => false
    ]); ?>
    <?php
    $form = \yii\bootstrap4\ActiveForm::begin([
        'options' => ['data-pjax' => true],
        'action' => ['/order/payment-actions/update', 'id' => $model->pay_id],
        'method' => 'post'
    ]);
    ?>

    <?php //= $form->field($model, 'pay_type_id')->textInput() ?>

    <?= $form->field($model, 'pay_method_id')->dropdownList(PaymentMethod::getList(), ['prompt' => '---'])->label('Method') ?>

    <?= $form->field($model, 'pay_status_id')->dropDownList(Payment::getStatusList(), ['prompt' => '---'])->label('Status') ?>

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

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php \yii\bootstrap4\ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
