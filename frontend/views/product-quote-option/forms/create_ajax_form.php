<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProductQuoteOption */
/* @var $form yii\widgets\ActiveForm */

$pjaxId = 'pjax-add-product-quote-option'; // . uniqid();
?>

<div class="product-quote-option-create-form-ajax">
    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
        // $form = ActiveForm::begin(['']);
        $form = ActiveForm::begin([
            'id' => 'product-quote-option-form',
            'options' => ['data-pjax' => true],
            'action' => ['product-quote-option/create-ajax'],
            'method' => 'post'
        ]);
        ?>

        <?= $form->errorSummary($model)?>

        <?= $form->field($model, 'pqo_product_quote_id')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'pqo_product_option_id')->dropDownList(\common\models\ProductOption::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'pqo_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pqo_description')->textarea(['rows' => 3]) ?>

        <?//= $form->field($model, 'pqo_status_id')->dropDownList(\common\models\ProductQuoteOption::getStatusList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'pqo_price')->input('number', ['min' => 0, 'max' => 1000, 'step' => 0.01]) ?>

        <?//= $form->field($model, 'pqo_client_price')->input('number', ['min' => 0, 'max' => 1000, 'step' => 0.01]) ?>

        <?= $form->field($model, 'pqo_extra_markup')->input('number', ['min' => 0, 'max' => 1000, 'step' => 0.01]) ?>


        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-plus-circle"></i> Create Option', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
