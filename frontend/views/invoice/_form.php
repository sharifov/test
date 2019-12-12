<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inv_gid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inv_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inv_order_id')->textInput() ?>

    <?= $form->field($model, 'inv_status_id')->textInput() ?>

    <?= $form->field($model, 'inv_sum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inv_client_sum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inv_client_currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inv_currency_rate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inv_description')->textarea(['rows' => 6]) ?>

    <?/*= $form->field($model, 'inv_created_user_id')->textInput() ?>

    <?= $form->field($model, 'inv_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'inv_created_dt')->textInput() ?>

    <?= $form->field($model, 'inv_updated_dt')->textInput()*/ ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
