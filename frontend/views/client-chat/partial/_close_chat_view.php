<?php

use sales\model\clientChat\useCase\close\ClientChatCloseForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var ClientChatCloseForm $closeForm */
?>

	<div class="row">
		<div class="col-md-12">
			<?php Pjax::begin(['id' => 'pjax-cc-submit-close', 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]) ?>
			<?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1, 'id' => 'cc-submit-close-form']]); ?>
			<?= $form->errorSummary($closeForm) ?>

			<?= $form->field($closeForm, 'cchId')->hiddenInput()->label(false) ?>

            <?php if ($closeForm->reasons): ?>
			    <?= $form->field($closeForm, 'reasonId')->dropDownList($closeForm->getReasonList()) ?>

			    <?= $form->field($closeForm, 'comment')->textarea(['max' => 100]) ?>
            <?php endif ?>

			<div class="text-center" style="width: 100%">
				<?= Html::submitButton('Submit', ['class' => 'btn btn-success _cc_submit_close']) ?>
				<?= Html::button('Cancel', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
			</div>

			<?php $form::end(); ?>
			<?php Pjax::end() ?>
		</div>
	</div>

<?php

$js = <<<JS
;(function() {
    let btnHtml = '';
    $('#pjax-cc-submit-close').on('pjax:beforeSend', function (obj, xhr, data) {
        btnHtml = $('._cc_submit_close').html();
        $('._cc_submit_close').html('<i class="fa fa-spin fa-spinner"></i>');
    });
    $('#pjax-cc-submit-close').on('pjax:end', function (data, xhr) {
        $('._cc_submit_close').html(btnHtml);
        if (xhr.status === 500) {
            createNotify('Error', 'Internal Server Error', 'error');
        } else if (xhr.status === 403) {
            createNotify('Error', xhr.responseText, 'error');
        }
    });
})();
JS;
$this->registerJs($js);
