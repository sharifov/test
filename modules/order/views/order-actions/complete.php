<?php

use modules\order\src\useCase\orderComplete\CompleteForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model CompleteForm */
/* @var $form yii\bootstrap4\ActiveForm */

$pjaxId = 'pjax-order-complete-form-' . $model->orderId;
?>

<div class="order-form">

    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
        $form = ActiveForm::begin([
            'options' => ['data-pjax' => true],
            'action' => ['/order/order-actions/complete', 'orderId' => $model->orderId],
            'method' => 'post'
        ]);
        ?>

        <?php echo $form->errorSummary($model) ?>

        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
