<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTask */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="object-task-form col-4">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ot_uuid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ot_q_id')->textInput() ?>

    <?= $form->field($model, 'ot_object')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ot_object_id')->textInput() ?>

    <?= $form->field($model, 'ot_execution_dt')->textInput() ?>

    <?= $form->field($model, 'ot_command')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ot_status')->textInput() ?>

    <?= $form->field($model, 'ot_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
