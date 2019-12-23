<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="offer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'of_gid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'of_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'of_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'of_lead_id')->textInput() ?>

    <?= $form->field($model, 'of_status_id')->textInput() ?>

    <?= $form->field($model, 'of_owner_user_id')->textInput() ?>

    <?= $form->field($model, 'of_client_currency')->dropDownList(\common\models\Currency::getList(), ['prompt' => '---']) ?>

    <?= $form->field($model, 'of_client_currency_rate')->input('number', ['min' => 0, 'max' => 100, 'step' => 0.00001]) ?>

    <?= $form->field($model, 'of_app_total')->input('number', ['min' => 0, 'max' => 100000, 'step' => 0.01]) ?>

    <?= $form->field($model, 'of_client_total')->input('number', ['min' => 0, 'max' => 100000, 'step' => 0.01]) ?>



    <?/*= $form->field($model, 'of_created_user_id')->textInput() ?>

    <?= $form->field($model, 'of_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'of_created_dt')->textInput() ?>

    <?= $form->field($model, 'of_updated_dt')->textInput()*/ ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
