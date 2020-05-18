<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserContactList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-contact-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ucl_user_id')->textInput() ?>

    <?= $form->field($model, 'ucl_client_id')->textInput() ?>

    <?= $form->field($model, 'ucl_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ucl_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ucl_favorite')->dropDownList([0 => 'No', 1 => 'Yes']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
