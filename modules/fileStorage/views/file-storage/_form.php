<?php

use modules\fileStorage\FileStorageModule;
use sales\widgets\DateTimePicker;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileStorage\FileStorage */
/* @var $form ActiveForm */
?>

<div class="file-storage-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fs_uid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fs_mime_type')->dropDownList(FileStorageModule::getMimeTypes(), ['prompt' => 'Select type']) ?>

        <?= $form->field($model, 'fs_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fs_title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fs_path')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fs_size')->textInput() ?>

        <?= $form->field($model, 'fs_private')->checkbox() ?>

        <?= $form->field($model, 'fs_expired_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'fs_created_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
