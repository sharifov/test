<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Airline */
/* @var $form ActiveForm */
?>

<div class="airline-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'iata')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'iaco')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'countryCode')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cl_economy')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cl_premium_economy')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cl_business')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cl_premium_business')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cl_first')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cl_premium_first')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'updated_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
