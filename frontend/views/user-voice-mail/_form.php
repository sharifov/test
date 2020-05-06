<?php

use common\models\Employee;
use common\models\Language;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\userVoiceMail\entity\UserVoiceMail */
/* @var $form ActiveForm */
/* @var $languageList array */
?>

<div class="user-voice-mail-form">

    <div class="col-md-3">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'uvm_user_id')->dropDownList(Employee::getList(), ['prompt' => '--']) ?>

        <?= $form->field($model, 'uvm_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'uvm_say_text_message')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'uvm_say_language')->dropDownList($languageList) ?>

        <?= $form->field($model, 'uvm_say_voice')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'uvm_voice_file_message')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'uvm_record_enable')->checkbox() ?>

        <?= $form->field($model, 'uvm_max_recording_time')->input('number') ?>

        <?= $form->field($model, 'uvm_transcribe_enable')->checkbox() ?>

        <?= $form->field($model, 'uvm_enabled')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
