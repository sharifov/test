<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\EventHandler */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-handler-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'eh_el_id')->textInput() ?>

    <?= $form->field($model, 'eh_class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'eh_method')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'eh_enable_type')->textInput() ?>

    <?= $form->field($model, 'eh_enable_log')->textInput() ?>

    <?= $form->field($model, 'eh_asynch')->textInput() ?>

    <?= $form->field($model, 'eh_break')->textInput() ?>

    <?= $form->field($model, 'eh_sort_order')->textInput() ?>

    <?= $form->field($model, 'eh_cron_expression')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'eh_condition')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'eh_builder_json')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
