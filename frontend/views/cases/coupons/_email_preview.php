<?php

/** @var $previewEmailForm \frontend\models\CasePreviewEmailForm */
/** @var $case \sales\entities\cases\Cases */

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/coupon/send/'])
?>

<br>
<?php $form = \yii\bootstrap\ActiveForm::begin([
	'method' => 'post',
	'options' => [
		'data-pjax' => 1,
		'class' => 'panel-body',
	],
    'action' => $url
]);

echo $form->errorSummary($previewEmailForm);
?>

<?php /*<div class="modal fade" id="modal-email-preview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Email preview</h4>
                        </div>
                        <div class="modal-body">*/ ?>

<div class="row">
	<div class="col-sm-4 form-group">

		<?= $form->field($previewEmailForm, 'e_email_from')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
		<?= $form->field($previewEmailForm, 'e_email_from_name')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>


		<?= $form->field($previewEmailForm, 'e_case_id')->hiddenInput()->label(false); ?>
		<?= $form->field($previewEmailForm, 'e_language_id')->hiddenInput()->label(false); ?>
		<?= $form->field($previewEmailForm, 'e_email_tpl_id')->hiddenInput()->label(false); ?>
		<?= $form->field($previewEmailForm, 'e_quote_list')->hiddenInput()->label(false); ?>
	</div>
	<div class="col-sm-4 form-group">
		<?= $form->field($previewEmailForm, 'e_email_to')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
		<?= $form->field($previewEmailForm, 'e_email_to_name')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 form-group">
		<?= $form->field($previewEmailForm, 'e_email_subject')->textInput(['class' => 'form-control', 'maxlength' => true]) ?>
	</div>
</div>
<div class="form-group">
	<?= $form->field($previewEmailForm, 'e_email_message')->widget(\dosamigos\ckeditor\CKEditor::class, [
		'options' => [
			'rows' => 6,
			'readonly' => false
		],
		'preset' => 'custom',
		'clientOptions' => [
			'height' => 500,
			'fullPage' => true,

			'allowedContent' => true,
			'resize_enabled' => false,
			'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
			'removePlugins' => 'elementspath',
		]
	]) ?>
</div>
<div class="row" style="display: none" id="email-data-content-div">
    <pre>
        <?= json_encode($previewEmailForm->e_content_data) ?>
    </pre>
</div>

<div class="row">
	<div class="col-md-12">
		<?php $messageSize = mb_strlen($previewEmailForm->e_email_message) ?>
		<b>Content size: <?=Yii::$app->formatter->asShortSize($messageSize, 1) ?></b>
		<?php if($messageSize > 102 * 1024): ?>
			&nbsp;&nbsp;&nbsp;<span class="danger">Warning: recommended MAX content size: <b><?=Yii::$app->formatter->asShortSize(102 * 1024, 1) ?></b>.</span>
		<?php endif; ?>
		<hr>
	</div>
</div>

<div class="btn-wrapper text-right">
	<?= Html::submitButton('<i class="fa fa-envelope-o"></i> Send Email', ['class' => 'btn btn-lg btn-primary']) ?>
    <?= Html::button('<i class="fa fa-list"></i> Show Email data (for Admins)', ['class' => 'btn btn-lg btn-warning', 'onclick' => '$("#email-data-content-div").toggle()']) ?>
</div>
<?php \yii\bootstrap\ActiveForm::end(); ?>
