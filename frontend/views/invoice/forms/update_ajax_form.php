<?php

use frontend\models\form\InvoiceForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model InvoiceForm */
/* @var $form yii\bootstrap4\ActiveForm */

$pjaxId = 'pjax-invoice-form';
?>

<div class="invoice-form">

    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
    <?php
    $form = ActiveForm::begin([
        'options' => ['data-pjax' => true],
        'action' => ['/invoice/update-ajax', 'id' => $model->inv_id],
        'method' => 'post'
    ]);
    ?>

    <?php echo $form->errorSummary($model) ?>

    <?//= $form->field($model, 'inv_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'inv_order_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'inv_sum')->input('number', ['min' => -10000, 'max' => 20000, 'step' => 0.01]) ?>

    <?= $form->field($model, 'inv_status_id')->dropDownList(\common\models\Invoice::getStatusList(), ['prompt' => '---']) ?>

    <?= $form->field($model, 'inv_description')->textarea(['rows' => 2]) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Save Invoice', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
