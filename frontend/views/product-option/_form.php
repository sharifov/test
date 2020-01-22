<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProductOption */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-option-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <?= $form->field($model, 'po_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'po_product_type_id')->dropDownList(\common\models\ProductType::getListAll(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'po_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'po_description')->textarea(['rows' => 3]) ?>

        <?= $form->field($model, 'po_price_type_id')->dropDownList(\common\models\ProductOption::getPriceTypeList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'po_max_price')->input('number', ['min' => 0, 'max' => 10000, 'step' => 0.01]) ?>

        <?= $form->field($model, 'po_min_price')->input('number', ['min' => 0, 'max' => 10000, 'step' => 0.01]) ?>

        <?= $form->field($model, 'po_price')->input('number', ['min' => 0, 'max' => 10000, 'step' => 0.01]) ?>

        <?= $form->field($model, 'po_enabled')->checkbox() ?>

    <!--    --><?//= $form->field($model, 'po_created_user_id')->textInput() ?>
    <!---->
    <!--    --><?//= $form->field($model, 'po_updated_user_id')->textInput() ?>
    <!---->
    <!--    --><?//= $form->field($model, 'po_created_dt')->textInput() ?>
    <!---->
    <!--    --><?//= $form->field($model, 'po_updated_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
