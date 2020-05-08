<?php
use sales\model\userVoiceMail\entity\UserVoiceMail;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/**
 * @var UserVoiceMail $model
 */

?>

<script>
    pjaxOffFormSubmit('#create-user-voice-mail-pjax');
</script>

<?php Pjax::begin(['id' => 'create-user-voice-mail-pjax', 'timeout' => 2000, 'enablePushState' => false]); ?>
<?php $form = ActiveForm::begin([
	'options' => ['data-pjax' => true],
	'action' => ['user-voice-mail/ajax-create', 'uid' => $model->uvm_user_id],
	'method' => 'post',
]) ?>

<div class="col-md-12">

	<div style="display: none;">
		<?= $form->field($model, 'uvm_user_id')->hiddenInput()->label(false)?>
	</div>

    <?= $form->errorSummary($model) ?>

	<?= $form->field($model, 'uvm_name')->textInput() ?>
	<?= $form->field($model, 'uvm_say_text_message')->textarea() ?>
	<?= $form->field($model, 'uvm_say_language')->dropDownList(\common\models\Language::getList(), ['prompt' => '--'])?>
	<?= $form->field($model, 'uvm_say_voice')->textarea(['max' => 30]) ?>
	<?= $form->field($model, 'uvm_voice_file_message')->textarea(['max' => 255]) ?>
	<?= $form->field($model, 'uvm_record_enable')->checkbox() ?>
	<?= $form->field($model, 'uvm_max_recording_time')->input('number') ?>
	<?= $form->field($model, 'uvm_transcribe_enable')->checkbox() ?>
	<?= $form->field($model, 'uvm_enabled')->checkbox() ?>

	<div class="form-group text-center">
		<?= Html::submitButton('Save', ['class' => 'btn btn-success'])?>
	</div>
</div>

<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
