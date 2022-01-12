<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\UserFeedbackFile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-feedback-file-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'uff_uf_id')->textInput() ?>

    <?= $form->field($model, 'uff_mimetype')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uff_size')->textInput() ?>

    <?= $form->field($model, 'uff_filename')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uff_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uff_blob')->textInput() ?>

    <?= $form->field($model, 'uff_created_dt')->textInput() ?>

    <?= $form->field($model, 'uff_created_user_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
