<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogRecord\CallLogRecord */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-log-record-form">

    <div class="row">
        <div class="col-md-4">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'clr_cl_id')->textInput() ?>

            <?= $form->field($model, 'clr_record_sid')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'clr_duration')->textInput() ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
