<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\taskList\TaskList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tl_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tl_object')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tl_condition')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tl_condition_json')->textInput() ?>

    <?= $form->field($model, 'tl_params_json')->textInput() ?>

    <?= $form->field($model, 'tl_work_start_time_utc')->textInput() ?>

    <?= $form->field($model, 'tl_work_end_time_utc')->textInput() ?>

    <?= $form->field($model, 'tl_duration_min')->textInput() ?>

    <?= $form->field($model, 'tl_enable_type')->textInput() ?>

    <?= $form->field($model, 'tl_cron_expression')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tl_sort_order')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
