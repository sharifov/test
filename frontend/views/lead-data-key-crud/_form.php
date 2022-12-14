<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadDataKey\entity\LeadDataKey */
/* @var $form ActiveForm */
?>

<div class="lead-data-key-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?php if (!$model->ldk_is_system) : ?>
            <?= $form->field($model, 'ldk_key')->textInput(['maxlength' => true]) ?>
        <?php endif ?>

        <?= $form->field($model, 'ldk_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ldk_enable')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
