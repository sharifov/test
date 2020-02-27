<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LeadChecklistType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-checklist-type-form">

    <div class="col-md-4">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lct_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lct_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lct_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lct_enabled')->checkbox() ?>

    <?= $form->field($model, 'lct_sort_order')->input('number', ['min' => 0, 'step' => 1]) ?>

    <?php /*= $form->field($model, 'lct_updated_dt')->textInput() ?>

    <?= $form->field($model, 'lct_updated_user_id')->textInput()*/ ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>

</div>
