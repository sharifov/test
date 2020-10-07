<?php

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\hold\ClientChatHoldForm;
use sales\model\clientChatHold\service\ClientChatHoldService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var ClientChat $clientChat */
/** @var ClientChatHoldForm $holdForm */
/** @var array $deadlineOptions */
?>

<div class="row">
	<div class="col-md-12">
        <?php Pjax::begin(
            [
                'id' => 'pjax-cc-submit-hold',
                'timeout' => 5000,
                'enablePushState' => false,
                'enableReplaceState' => false,
            ]
        ) ?>
        <?php $form = ActiveForm::begin([
                'id' => 'cc-submit-hold-form',
                'options' => ['data-pjax' => 1]
            ]); ?>
                <?php echo  $form->errorSummary($holdForm) ?>

                <?php echo  $form->field($holdForm, 'cchId')->hiddenInput()->label(false) ?>

                <?php echo $form->field($holdForm, 'minuteToDeadline')->dropDownList(
                    (new ClientChatHoldService())->formattedDeadlineOptions(),
                    ['prompt' => '---']) ?>

                <?php if ($holdForm->reasons): ?>
                    <?= $form->field($holdForm, 'reasonId')->dropDownList($holdForm->getReasonList()) ?>

                    <?= $form->field($holdForm, 'comment', ['enableClientValidation' => false])->textarea(['max' => 100]) ?>
                <?php endif ?>

                <div class="text-center" style="width: 100%">
                    <?php echo  Html::submitButton('Submit', ['class' => 'btn btn-success _cc_submit_hold']) ?>
                </div>    
            <?php $form::end() ?>
        <?php Pjax::end() ?>
	</div>
</div>

<?php

$js = <<<JS
(function() {
    let btnHtml = '';
    
    $('#pjax-cc-submit-hold').on('pjax:beforeSend', function (obj, xhr, data) {
        data.data.append('cchId', $('#clientchatholdform-cchid').val());
        btnHtml = $('._cc_submit_hold').html();
        $('._cc_submit_hold').html('<i class="fa fa-spin fa-spinner"></i>');
    });
    
    $('#pjax-cc-submit-hold').on('pjax:end', function (data, xhr) {
        $('._cc_submit_hold').html(btnHtml);
        if (xhr.status !== 200) {
            createNotify('Error', xhr.responseText, 'error');
        } 
    }); 
})();
JS;
$this->registerJs($js);
