<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightRequestLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-request-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'flr_fr_id')->textInput() ?>

    <?= $form->field($model, 'flr_status_id_old')->textInput() ?>

    <?= $form->field($model, 'flr_status_id_new')->textInput() ?>

    <?= $form->field($model, 'flr_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'flr_created_dt')->textInput() ?>

    <?= $form->field($model, 'flr_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
