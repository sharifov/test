<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-quote-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'atnq_attraction_id')->textInput() ?>

    <?= $form->field($model, 'atnq_hash_key')->textInput() ?>

    <?= $form->field($model, 'atnq_product_quote_id')->textInput() ?>

    <?= $form->field($model, 'atnq_attraction_name')->textInput() ?>

    <?= $form->field($model, 'atnq_supplier_name')->textInput() ?>

    <?= $form->field($model, 'atnq_type_name')->textInput() ?>
    <?= $form->field($model, 'atnq_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
