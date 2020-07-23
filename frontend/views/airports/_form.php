<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Airports */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="airports-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'iata')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'timezone')->widget(\kartik\select2\Select2::class, [
        'data' => \common\models\Employee::timezoneList(true),
        'size' => \kartik\select2\Select2::SMALL,
        'options' => ['placeholder' => 'Select TimeZone', 'multiple' => false],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>


    <?= $form->field($model, 'dst')->textInput() ?>

    <?= $form->field($model, 'a_created_user_id')->textInput() ?>

    <?= $form->field($model, 'a_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'a_icao')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'a_state')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'a_rank')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'a_multicity')->textInput() ?>

    <?= $form->field($model, 'a_close')->textInput() ?>

    <?= $form->field($model, 'a_disabled')->textInput() ?>

    <?= $form->field($model, 'a_created_dt')->textInput() ?>

    <?= $form->field($model, 'a_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
