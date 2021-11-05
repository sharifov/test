<?php

/**
 * @var $form \modules\product\src\forms\ChangeQuoteSendEmailForm
 * @var $case \sales\entities\cases\Cases
 * @var $this \yii\web\View
 * @var $order \modules\order\src\entities\order\Order
 */

use modules\product\src\forms\ChangeQuoteSendEmailForm;
use yii\helpers\Html;

$clientEmails = [];
foreach ($order->orderContacts as $orderContact) {
    $clientEmails[$orderContact->oc_email] = $orderContact->oc_email;
}
$clientEmails = \yii\helpers\ArrayHelper::merge($clientEmails, $case->client ? $case->client->getEmailList() : []);
?>
<script>
    pjaxOffFormSubmit('#reprotection_quote_choose_cliet_pjax');
</script>
<?php
\yii\widgets\Pjax::begin([
    'id' => 'reprotection_quote_choose_cliet_pjax',
    'enablePushState' => false,
    'enableReplaceState' => false,
    'timeout' => 3000
]);
?>
<?php $activeForm = \yii\bootstrap\ActiveForm::begin([
    'method' => 'post',
    'options' => [
        'data-pjax' => 1,
        'class' => 'panel-body',
    ],
    'id' => 'reprotection_quote_preview_email_form',
    'enableClientValidation' => false
]);

echo $activeForm->errorSummary($form);
?>

<?= $activeForm->field($form, 'caseId')->hiddenInput()->label(false) ?>

    <div class="row">
        <div class="col-sm-12 form-group">
            <?= $activeForm->field($form, 'clientEmail')->dropDownList($clientEmails) ?>
        </div>
    </div>

    <div class="btn-wrapper text-right">
        <?= Html::button('<i class="fa fa-close"></i> Cancel', ['class' => 'btn btn-sm btn-danger', 'data-dismiss' => 'modal']) ?>
        <?= Html::submitButton('<i class="fa fa-envelope-o"></i> Preview Email', ['class' => 'btn btn-sm btn-success', 'id' => 'reprotection-quote-preview-email-btn']) ?>
    </div>
<?php \yii\bootstrap\ActiveForm::end(); ?>

<?php \yii\widgets\Pjax::end() ?>

<?php
$js = <<<JS
$("#reprotection_quote_choose_cliet_pjax").on("pjax:start", function() {
    $('#reprotection-quote-preview-email-btn').find('i').replaceWith('<i class="fa fa-spin fa-spinner"></i>');
    $('#reprotection-quote-preview-email-btn').addClass('disabled').prop('disabled', true);
});
JS;
$this->registerJs($js);
