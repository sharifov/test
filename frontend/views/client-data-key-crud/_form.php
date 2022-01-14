<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\clientDataKey\entity\ClientDataKey */
/* @var $form ActiveForm */
?>

<div class="client-data-key-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cdk_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cdk_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cdk_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cdk_enable')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
