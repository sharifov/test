<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionPax */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attraction-pax-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'atnp_atn_id')->textInput() ?>

    <?= $form->field($model, 'atnp_type_id')->textInput() ?>

    <?= $form->field($model, 'atnp_age')->textInput() ?>

    <?= $form->field($model, 'atnp_first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'atnp_last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'atnp_dob')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
