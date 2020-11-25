<?php

use common\models\Language;
use sales\model\userVoiceMail\useCase\manage\UserVoiceMailForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/**
 * @var $this \yii\web\View
 * @var UserVoiceMailForm $model
 */
\frontend\assets\WebAudioRecorder::register($this);
?>

<script>
    pjaxOffFormSubmit('#create-user-voice-mail-pjax');
</script>

<?php Pjax::begin(['id' => 'create-user-voice-mail-pjax', 'timeout' => 2000, 'enablePushState' => false]); ?>
<?php $form = ActiveForm::begin([
    'options' => ['data-pjax' => true, 'enctype' => 'multipart/form-data'],
    'action' => ['user-voice-mail/ajax-create', 'uid' => $model->uvm_user_id],
    'method' => 'post',
    'enableClientValidation' => true,
]) ?>

<div class="col-md-12">

    <div style="display: none;">
        <?= $form->field($model, 'uvm_user_id')->hiddenInput()->label(false)?>
    </div>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'uvm_name')->textInput() ?>
    <?= $form->field($model, 'uvm_say_text_message')->textarea() ?>
    <?= $form->field($model, 'uvm_say_language')->dropDownList(Language::getListByPk($model->getAllowedList()), ['prompt' => '--'])?>
    <?= $form->field($model, 'uvm_say_voice')->dropDownList($model->getSaveVoiceList(), ['prompt' => '--']) ?>
    <?= $form->field($model, 'uvm_record_enable')->checkbox() ?>
    <?= $form->field($model, 'uvm_max_recording_time')->input('number') ?>
    <?php //= $form->field($model, 'uvm_transcribe_enable')->checkbox() ?>
    <?php //= $form->field($model, 'uvm_enabled')->checkbox() ?>

    <div id="webAudioRecorder">
        <div style="display: flex; align-items: center;">
            <?= Html::button('<i class="fa fa-microphone"></i> Record', ['id' => 'recordButton', 'class' => 'btn btn-success']) ?>
            <?= Html::button('<i class="fa fa-stop"></i> Stop', ['id' => 'stopButton', 'class' => 'btn btn-danger', 'disabled' => true]) ?>
        </div>
        <div id="timer-wrapper" class="d-flex align-items-center">
            <span id="time-display" class="label label-default" style="margin-right: 10px;">00:00</span>
            <canvas id="visualizer" height="20px" style="width: 100%;"></canvas>
        </div>
    </div>

    <div class="form-group text-center">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success'])?>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php $js = <<<JS
var webAudioRecord = $('#webAudioRecorder').webAudioRecorder({
    recordBtnSelector: '#recordButton',
    stopBtnSelector: '#stopButton',
    showFormatsInfo: false,
    showLog: true,
    blobUrl: '{$model->blobUrl}'
});
$('#create-user-voice-mail-pjax').off().on('pjax:beforeSend', function (xhr, data, settings) {

settings.data.append('{$model->formName()}[recordFile]', webAudioRecord.getRecord());
settings.data.append('{$model->formName()}[blobUrl]', webAudioRecord.getBlobUrl());
    
});
JS;
$this->registerJs($js);
?>
<?php Pjax::end(); ?>
