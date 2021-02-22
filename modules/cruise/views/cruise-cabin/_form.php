<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseCabin\CruiseCabin */
/* @var $form ActiveForm */
?>

<div class="cruise-cabin-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'crc_cruise_id')->textInput() ?>

        <?= $form->field($model, 'crc_name')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
