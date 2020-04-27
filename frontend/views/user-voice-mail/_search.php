<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\userVoiceMail\entity\search\UserVoiceMailSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="user-voice-mail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'uvm_id') ?>

    <?= $form->field($model, 'uvm_user_id') ?>

    <?= $form->field($model, 'uvm_name') ?>

    <?= $form->field($model, 'uvm_say_text_message') ?>

    <?= $form->field($model, 'uvm_say_language') ?>

    <?php // echo $form->field($model, 'uvm_say_voice') ?>

    <?php // echo $form->field($model, 'uvm_voice_file_message') ?>

    <?php // echo $form->field($model, 'uvm_record_enable') ?>

    <?php // echo $form->field($model, 'uvm_max_recording_time') ?>

    <?php // echo $form->field($model, 'uvm_transcribe_enable') ?>

    <?php // echo $form->field($model, 'uvm_enabled') ?>

    <?php // echo $form->field($model, 'uvm_created_dt') ?>

    <?php // echo $form->field($model, 'uvm_updated_dt') ?>

    <?php // echo $form->field($model, 'uvm_created_user_id') ?>

    <?php // echo $form->field($model, 'uvm_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
