<?php

use sales\forms\clientChat\RealTimeStartChatForm;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var $startChatForm RealTimeStartChatForm */
/** @var $channels array */
/** @var $domainError string */
?>

<script>pjaxOffFormSubmit('#_cc_real_time_start_chat_pjax')</script>
<div class="row">
	<div class="col-md-12">
        <?php if (!$domainError): ?>
            <?php Pjax::begin(['enableReplaceState' => false, 'enablePushState' => false, 'timeout' => 5000, 'id' => '_cc_real_time_start_chat_pjax']) ?>
                <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

                    <?= $form->errorSummary($startChatForm) ?>

                    <?= $form->field($startChatForm, 'rid')->hiddenInput()->label(false) ?>
                    <?= $form->field($startChatForm, 'visitorId')->hiddenInput()->label(false) ?>
                    <?= $form->field($startChatForm, 'projectId')->hiddenInput()->label(false) ?>
                    <?= $form->field($startChatForm, 'projectName')->hiddenInput()->label(false) ?>
                    <?= $form->field($startChatForm, 'visitorName')->hiddenInput()->label(false) ?>

                    <?= $form->field($startChatForm, 'channelId')->widget(\kartik\select2\Select2::class, [
//                        "options" => [
//                            'prompt' => '---'
//                        ],
                        'data' => $channels
                    ]) ?>

                    <?= $form->field($startChatForm, 'message')->textarea() ?>

                    <div class="d-flex justify-content-center">
                        <?= \yii\helpers\Html::submitButton('Start Chat', ['class' => 'btn btn-success btn-sm', 'id' => '_cc_submit_send_message']) ?>
                    </div>

                <?php ActiveForm::end() ?>
            <?php Pjax::end(); ?>
        <?php else: ?>
            <?= \yii\bootstrap4\Alert::widget([
				'options' => [
					'class' => 'alert alert-danger'
				],
				'body' => $domainError
			]) ?>
        <?php endif; ?>
	</div>
</div>

<?php
$js = <<<JS
(function() {
    let btnHtml = '';
    $('#_cc_real_time_start_chat_pjax').on('pjax:end', function (data, xhr) {
        $('#_cc_submit_send_message').prop('disabled', false).removeClass('disabled').html(btnHtml);
    });
    $('#_cc_real_time_start_chat_pjax').on('pjax:beforeSend', function (obj, xhr, data) {
        btnHtml = $('#_cc_submit_send_message').html();
        $('#_cc_submit_send_message').addClass('disabled').prop('disabled', true).html('<i class="fa fa-spin fa-spinner"></i>');
    });
})();
JS;
$this->registerJs($js);
?>
