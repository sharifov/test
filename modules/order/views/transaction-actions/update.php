<?php

use common\models\Transaction;
use frontend\extensions\DatePicker;
use yii\helpers\Html;

/** @var Transaction $model */

$pjaxId = 'pjax-transaction-update-form-' . $model->tr_id;
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
            'action' => ['/order/transaction-actions/update', 'id' => $model->tr_id],
            'method' => 'post'
        ]);
        ?>

        <?= $form->field($model, 'tr_code')->textInput() ?>

        <?= $form->field($model, 'tr_invoice_id')->textInput() ?>

        <?= $form->field($model, 'tr_payment_id')->textInput() ?>

        <?= $form->field($model, 'tr_type_id')->dropdownList(Transaction::getTypeList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'tr_amount')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tr_currency')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tr_comment')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tr_date')->widget(DatePicker::class, [
                'clientOptions' => [
                    'format' => 'yyyy-mm-dd',
                ]
            ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    <?php \yii\bootstrap4\ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
