<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CreditCard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="credit-card-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">

        <?= $form->field($model, 'cc_number')->textInput(['maxlength' => true]) ?>

        <?//= $form->field($model, 'cc_display_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cc_holder_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cc_expiration_month')->input('number', ['min' => 1, 'max' => 12]) ?>

        <?= $form->field($model, 'cc_expiration_year')->input('number', ['min' => (int) date('Y', strtotime('-5 year')), 'max' => (int) date('Y', strtotime('+5 year'))]) ?>

        <?= $form->field($model, 'cc_cvv')->input('number', ['min' => 100, 'max' => 999]) ?>

        <?= $form->field($model, 'cc_type_id')->dropDownList(\common\models\CreditCard::getTypeList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'cc_status_id')->dropDownList(\common\models\CreditCard::getTypeList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'cc_is_expired')->checkbox() ?>

    <!--    --><?//= $form->field($model, 'cc_created_user_id')->textInput() ?>
    <!---->
    <!--    --><?//= $form->field($model, 'cc_updated_user_id')->textInput() ?>
    <!---->
    <!--    --><?//= $form->field($model, 'cc_created_dt')->textInput() ?>
    <!---->
    <!--    --><?//= $form->field($model, 'cc_updated_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
