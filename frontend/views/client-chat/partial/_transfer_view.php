<?php

use common\models\Department;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\transfer\ClientChatTransferForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var ClientChat $clientChat */
/** @var ClientChatTransferForm $transferForm */
?>

<script>pjaxOffFormSubmit('#pjax-cc-submit-transfer')</script>
<div class="row">
	<div class="col-md-12">
        <?php Pjax::begin(['id' => 'pjax-cc-submit-transfer', 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]) ?>
            <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>
                <?= $form->errorSummary($transferForm) ?>

                <?= $form->field($transferForm, 'cchId')->hiddenInput()->label(false) ?>

                <?= $form->field($transferForm, 'depId')->dropDownList(Department::getList(), ['prompt' => ' -- Select department --']) ?>

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
    $('#pjax-cc-submit-transfer').on('pjax:start', function () {
        btnHtml = $('._cc_submit_transfer').html();
        $('._cc_submit_transfer').html('<i class="fa fa-spin fa-spinner"></i>');
    });
    
    $('#pjax-cc-submit-transfer').on('pjax:end', function (data, xhr) {
        $('._cc_submit_transfer').html(btnHtml);
        
        if (xhr.status === 500) {
            createNotify('Error', 'Internal Server Error', 'error');
        }
    });
})();
JS;
$this->registerJs($js);
