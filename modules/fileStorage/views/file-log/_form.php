<?php

use modules\fileStorage\src\entity\fileLog\FileLogType;
use sales\widgets\DateTimePicker;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileLog\FileLog */
/* @var $form ActiveForm */
?>

<div class="file-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fl_fs_id')->textInput() ?>

        <?= $form->field($model, 'fl_fsh_id')->textInput() ?>

        <?= $form->field($model, 'fl_type_id')->dropDownList(FileLogType::getList(), ['prompt' => 'Select type']) ?>

        <?= $form->field($model, 'fl_created_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'fl_ip_address')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fl_user_agent')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
