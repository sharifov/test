<?php

use common\models\Conference;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Conference */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="conference-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cf_cr_id')->textInput() ?>

        <?= $form->field($model, 'cf_sid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cf_status_id')->dropDownList(Conference::STATUS_LIST, ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'cf_options')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'cf_start_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'cf_end_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'cf_duration')->textInput() ?>

        <?= $form->field($model, 'cf_created_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'cf_updated_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'cf_created_user_id')->textInput() ?>

        <?= $form->field($model, 'cf_recording_disabled')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>