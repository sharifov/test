<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTaskStatusLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="object-task-status-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'otsl_ot_uuid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'otsl_old_status')->textInput() ?>

    <?= $form->field($model, 'otsl_new_status')->textInput() ?>

    <?= $form->field($model, 'otsl_description')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
