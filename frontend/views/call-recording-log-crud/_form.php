<?php

use src\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\callRecordingLog\entity\CallRecordingLog */
/* @var $form ActiveForm */
?>

<div class="call-recording-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'crl_id')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'crl_call_sid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'crl_user_id')->widget(UserSelect2Widget::class) ?>

        <?= $form->field($model, 'crl_year')->textInput() ?>

        <?= $form->field($model, 'crl_month')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
