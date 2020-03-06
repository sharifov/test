<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\Flight */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">

        <?= $form->field($model, 'fl_product_id')->textInput() ?>

        <?= $form->field($model, 'fl_trip_type_id')->textInput() ?>

        <?= $form->field($model, 'fl_cabin_class')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fl_adults')->textInput() ?>

        <?= $form->field($model, 'fl_children')->textInput() ?>

        <?= $form->field($model, 'fl_infants')->textInput() ?>

        <?= $form->field($model, 'fl_stops')->input('number', ['min' => 0, 'max' => 9]) ?>

        <?= $form->field($model, 'fl_delayed_charge')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>