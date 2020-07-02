<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatData\entity\ClientChatData */
/* @var $form ActiveForm */
?>

<div class="client-chat-data-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccd_cch_id')->textInput() ?>

        <?= $form->field($model, 'ccd_country')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccd_region')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccd_city')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccd_latitude')->textInput() ?>

        <?= $form->field($model, 'ccd_longitude')->textInput() ?>

        <?= $form->field($model, 'ccd_url')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccd_title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccd_referrer')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccd_timezone')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccd_local_time')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
