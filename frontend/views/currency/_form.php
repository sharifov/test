<?php

use common\models\Currency;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Currency */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
        <?= $form->field($model, 'cur_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cur_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cur_symbol')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cur_enabled')->checkbox([
            'disabled' => $model->cur_code === Currency::getDefaultCurrencyCode()
        ]) ?>
        </div>
        <div class="col-md-2">

        <?= $form->field($model, 'cur_base_rate')->input('number', ['step' => 0.00001]) ?>

        <?= $form->field($model, 'cur_app_rate')->input('number', ['step' => 0.00001]) ?>

        <?= $form->field($model, 'cur_app_percent')->input('number', ['step' => 0.001]) ?>

        <?= $form->field($model, 'cur_sort_order')->dropDownList(range(0, 10), range(0, 10)) ?>





        <?php //= $form->field($model, 'cur_created_dt')->textInput() ?>

        <?php //= $form->field($model, 'cur_updated_dt')->textInput() ?>

        <?php //= $form->field($model, 'cur_synch_dt')->textInput() ?>
        </div>
    </div>

    <div class="clearfix"></div>
    <div class="form-group">
        <?= Html::submitButton('Save Currency', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
