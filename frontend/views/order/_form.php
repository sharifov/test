<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'or_gid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'or_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'or_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'or_lead_id')->textInput() ?>

    <?= $form->field($model, 'or_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'or_status_id')->textInput() ?>

    <?= $form->field($model, 'or_pay_status_id')->textInput() ?>

    <?= $form->field($model, 'or_app_total')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'or_app_markup')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'or_agent_markup')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'or_client_total')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'or_client_currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'or_client_currency_rate')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'or_owner_user_id')->textInput() ?>

    <?= $form->field($model, 'or_created_user_id')->textInput() ?>

    <?= $form->field($model, 'or_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'or_created_dt')->textInput() ?>

    <?= $form->field($model, 'or_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
