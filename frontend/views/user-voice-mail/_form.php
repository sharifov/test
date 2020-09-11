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

\frontend\assets\WebAudioRecorder::register($this);
?>

<div class="user-voice-mail-form">

    <div class="col-md-3">

    <?php \yii\widgets\Pjax::begin(['id' => 'user-voice-mail-form', 'timeout' => 2000, 'enablePushState' => false]) ?>

        <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

        <?= $form->field($model, 'uvm_user_id')->dropDownList(Employee::getList(), ['prompt' => '--']) ?>

        <?= $form->field($model, 'uvm_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'uvm_say_text_message')->textarea(['rows' => 6]) ?>

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

        <?= $form->field($model, 'uvm_transcribe_enable')->checkbox() ?>

        <?php //= $form->field($model, 'uvm_enabled')->checkbox() ?>

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

        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
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

$('#user-voice-mail-form').on('pjax:beforeSend', function (xhr, data, settings) {

settings.data.append('{$model->formName()}[recordFile]', webAudioRecord.getRecord());
settings.data.append('{$model->formName()}[blobUrl]', webAudioRecord.getBlobUrl());
    
});
JS;
$this->registerJs($js);
?>
        <?php \yii\widgets\Pjax::end() ?>
    </div>

</div>
