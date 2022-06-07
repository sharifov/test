<?php

use common\models\Airline;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DateSensitive */
/* @var $form yii\widgets\ActiveForm */

?>


<div class="date-sensitive-form row">

    <div class="col-md-6">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'da_key')->textInput() ?>

        <?= $form->field($model, 'da_name')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>


</div>
