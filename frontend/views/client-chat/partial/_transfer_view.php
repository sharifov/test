<?php

use common\models\Department;
use kartik\select2\Select2;
use sales\helpers\clientChat\ClientChatHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\transfer\ClientChatTransferForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var ClientChat $clientChat */
/** @var ClientChatTransferForm $transferForm */
?>

<div class="row">
	<div class="col-md-12">
        <?php Pjax::begin(['id' => 'pjax-cc-submit-transfer', 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false, 'clientOptions' => ['async' => false]]) ?>
            <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1, 'id' => 'cc-submit-transfer-form']]); ?>
                <?= $form->errorSummary($transferForm) ?>

                <?= $form->field($transferForm, 'cchId')->hiddenInput()->label(false) ?>

                <?= $form->field($transferForm, 'pjaxReload')->hiddenInput(['id' => 'pjaxReload'])->label(false) ?>

                <?= $form->field($transferForm, 'isOnline')->hiddenInput()->label(false) ?>

                <?= $form->field($transferForm, 'depId')->dropDownList(Department::getList(), ['prompt' => ' -- Select department --', 'id' => 'depId']) ?>

                <?= $form->field($transferForm, 'agentId')->widget(Select2::class, [
                    'data' => ClientChatHelper::getAvailableAgentForTransfer($clientChat, $clientChat->cch_dep_id),
                    'options' => [
                        'prompt' => '---',
                        'placeholder' => 'Select agent',
                        'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
						'placeholder' => 'Select agent',
                        'allowMultiple' => true
					],
                    'size' => Select2::SIZE_SMALL,
				]) ?>

        		<?= $form->field($transferForm, 'comment')->textarea(['max' => 255]) ?>

                <div class="text-center" style="width: 100%">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-success _cc_submit_transfer']) ?>
                </div>

            <?php $form::end(); ?>
        <?php Pjax::end() ?>
	</div>
</div>

<?php

$js = <<<JS
(function() {
    let btnHtml = '';
    $('#pjax-cc-submit-transfer').on('pjax:end', function (data, xhr) {
        $('._cc_submit_transfer').html(btnHtml);
        if (xhr.status === 500) {
            createNotify('Error', 'Internal Server Error', 'error');
        } else if (xhr.status === 403) {
            createNotify('Error', xhr.responseText, 'error');
        }
    });
    $('#pjax-cc-submit-transfer').on('pjax:beforeSend', function (obj, xhr, data) {
        data.data.append('cchId', $('#clientchattransferform-cchid').val());
        btnHtml = $('._cc_submit_transfer').html();
        $('._cc_submit_transfer').html('<i class="fa fa-spin fa-spinner"></i>');
    });
    
    $(document).on('change', '#depId', function () {
        $('#pjaxReload').val(1);
         $('#cc-submit-transfer-form').submit();
        
    });
})();
JS;
$this->registerJs($js);
