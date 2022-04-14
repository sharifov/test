<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shiftCategory\ShiftCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-category-form">
    <div class="col-md-2">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'sc_name')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
