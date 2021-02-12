<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruise\Cruise */
/* @var $form ActiveForm */
?>

<div class="cruise-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'crs_product_id')->textInput() ?>

        <?= $form->field($model, 'crs_departure_date_from')->textInput() ?>

        <?= $form->field($model, 'crs_arrival_date_to')->textInput() ?>

        <?= $form->field($model, 'crs_destination_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'crs_destination_label')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
