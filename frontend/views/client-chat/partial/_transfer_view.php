<?php

use kartik\select2\Select2;
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
            <?php Pjax::begin([
                'id' => 'pjax-cc-submit-transfer',
                'timeout' => 5000,
                'enablePushState' => false,
                'enableReplaceState' => false,
                'clientOptions' => ['async' => false]
            ]) ?>
            <?php $form = ActiveForm::begin([
                'id' => 'cc-submit-transfer-form',
                'options' => ['data-pjax' => 1],
                'enableClientValidation' => false
            ]); ?>

            <?= $form->errorSummary($transferForm) ?>

            <?= $form->field($transferForm, 'pjaxReload')->hiddenInput(['id' => 'pjaxReload'])->label(false) ?>

            <?= $form->field($transferForm, 'type')->dropDownList($transferForm->getTypeList()) ?>

            <?= $form->field($transferForm, 'depId')->dropDownList($transferForm->getDepartments(), ['prompt' => '-- Select department --']) ?>

            <?= $form->field($transferForm, 'channelId')->dropDownList($transferForm->getChannels(), ['prompt' => '-- Select channel --']) ?>

            <?php if ($transferForm->isAgentTransfer()) : ?>
                <?= $form->field($transferForm, 'agentId', ['options' => ['class' => 'form-group required']])->widget(Select2::class, [
                    'data' => $transferForm->getAgents(),
                    'options' => [
                        'prompt' => '---',
                        'placeholder' => 'Select agent',
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'placeholder' => 'Select agent',
                        'allowMultiple' => true
                    ],
                    'pluginEvents' => [
                        "change" => "function() { 
                            $('.field-clientchattransferform-agentid').removeClass('has-error'); 
                            $('.field-clientchattransferform-agentid').find('.help-block').html(''); 
                        }",
                    ],
                    'size' => Select2::SIZE_SMALL,
                ]) ?>
            <?php endif;?>

            <?php if ($transferForm->reasons) : ?>
                <?= $form->field($transferForm, 'reasonId')->dropDownList($transferForm->getReasonList()) ?>

                <?= $form->field($transferForm, 'comment')->textarea(['max' => 100]) ?>
            <?php endif ?>

            <div class="text-center" style="width: 100%">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-success _cc_submit_transfer']) ?>
            </div>

            <?php $form::end(); ?>
            <?php Pjax::end() ?>
        </div>
    </div>

<?php

$typeInputId = Html::getInputId($transferForm, 'type');
$depInputId = Html::getInputId($transferForm, 'depId');
$channelInputId = Html::getInputId($transferForm, 'channelId');

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
        data.data.append('cchId', {$transferForm->chatId});
        btnHtml = $('._cc_submit_transfer').html();
        $('._cc_submit_transfer').html('<i class="fa fa-spin fa-spinner"></i>');
    });
    
    $(document).on('input', '#{$depInputId}', function () {
        reloadForm();
    });
    $(document).on('input', '#{$typeInputId}', function(e) {
        reloadForm();
    });
    $(document).on('input', '#{$channelInputId}', function(e) {
        reloadForm();
    });
    function reloadForm() {
        $('#pjaxReload').val(1);
        $('#cc-submit-transfer-form').submit();
    }
})();
JS;
$this->registerJs($js);
