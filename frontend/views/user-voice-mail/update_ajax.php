<?php

use common\models\Language;
use sales\model\userVoiceMail\useCase\manage\UserVoiceMailForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserVoiceMailForm */
/* @var $form yii\widgets\ActiveForm */

\frontend\assets\WebAudioRecorder::register($this);
?>

<script>
    pjaxOffFormSubmit('#update-user-voice-mail-pjax');
</script>

<?php \yii\widgets\Pjax::begin(['id' => 'update-user-voice-mail-pjax', 'timeout' => 2000, 'enablePushState' => false]); ?>
<?php $form = ActiveForm::begin([
	'options' => ['data-pjax' => true],
	'action' => ['user-voice-mail/ajax-update', 'id' => $model->uvm_id],
	'method' => 'post',
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
    <?php if ($model->uvm_voice_file_message): ?>
	<?= $form->field($model, 'uvm_voice_file_message')->hiddenInput(['max' => 255, 'id' => 'voice-file-message']) ?>
    <div class="d-flex justify-content-center align-items-center">
        <audio src="<?= $model->uvm_voice_file_message ?>" controls style="width: 100%; margin-right: 10px;"></audio>
        <?= Html::button('<i class="fa fa-trash"></i>', ['class' => 'delete-record btn btn-danger']); ?>
    </div>
    <?php endif; ?>
	<?= $form->field($model, 'uvm_record_enable')->checkbox() ?>
	<?= $form->field($model, 'uvm_max_recording_time')->input('number') ?>
	<?php //= $form->field($model, 'uvm_transcribe_enable')->checkbox() ?>
	<?= $form->field($model, 'uvm_enabled')->checkbox() ?>

    <div id="webAudioRecorder">
        <div>
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

$('.delete-record').on('click', function () {
    $(this).closest('div').remove(); 
    $('#voice-file-message').val('');
});

$('#update-user-voice-mail-pjax').on('pjax:beforeSend', function (xhr, data, settings) {

settings.data.append('{$model->formName()}[recordFile]', webAudioRecord.getRecord());
settings.data.append('{$model->formName()}[blobUrl]', webAudioRecord.getBlobUrl());
    
});
JS;
$this->registerJs($js);
?>
<?php \yii\widgets\Pjax::end(); ?>
