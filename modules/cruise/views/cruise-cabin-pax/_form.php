<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseCabinPax\CruiseCabinPax */
/* @var $form ActiveForm */
?>

<div class="cruise-cabin-pax-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'crp_cruise_cabin_id')->textInput() ?>

        <?= $form->field($model, 'crp_type_id')->textInput() ?>

        <?= $form->field($model, 'crp_age')->textInput() ?>

        <?= $form->field($model, 'crp_first_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'crp_last_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'crp_dob')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
