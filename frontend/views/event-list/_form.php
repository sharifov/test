<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\EventList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'el_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'el_category')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'el_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'el_enable_type')->textInput() ?>

    <?= $form->field($model, 'el_enable_log')->textInput() ?>

    <?= $form->field($model, 'el_break')->textInput() ?>

    <?= $form->field($model, 'el_sort_order')->textInput() ?>

    <?= $form->field($model, 'el_cron_expression')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'el_condition')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'el_builder_json')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
