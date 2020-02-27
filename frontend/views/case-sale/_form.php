<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CaseSale */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="case-sale-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
    <?= $form->field($model, 'css_cs_id')->textInput() ?>

    <?= $form->field($model, 'css_sale_id')->textInput() ?>

    <?= $form->field($model, 'css_sale_book_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'css_sale_pnr')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'css_sale_pax')->textInput() ?>

    <?= $form->field($model, 'css_sale_created_dt')->textInput() ?>

    <?= $form->field($model, 'css_sale_data')->textInput() ?>

    <?php //= $form->field($model, 'css_created_user_id')->textInput() ?>

    <?php //= $form->field($model, 'css_updated_user_id')->textInput() ?>

    <?php //= $form->field($model, 'css_created_dt')->textInput() ?>

    <?php //= $form->field($model, 'css_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
