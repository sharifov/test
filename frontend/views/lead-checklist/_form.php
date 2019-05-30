<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LeadChecklist */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-checklist-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lc_type_id')->textInput() ?>

    <?= $form->field($model, 'lc_lead_id')->textInput() ?>

    <?= $form->field($model, 'lc_user_id')->textInput() ?>

    <?= $form->field($model, 'lc_notes')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lc_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
