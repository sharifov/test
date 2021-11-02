<?php

/**
 * @var $form \modules\product\src\forms\VoluntaryRefundSendEmailForm
 * @var $case \sales\entities\cases\Cases
 * @var $this \yii\web\View
 * @var $order \modules\order\src\entities\order\Order
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

$clientEmails = [];
foreach ($order->orderContacts as $orderContact) {
    $clientEmails[$orderContact->oc_email] = $orderContact->oc_email;
}
$clientEmails = \yii\helpers\ArrayHelper::merge($clientEmails, $case->client ? $case->client->getEmailList() : []);
?>
    <script>
        pjaxOffFormSubmit('#voluntary_refund_choose_client_pjax');
    </script>
<?php
Pjax::begin([
    'id' => 'voluntary_refund_choose_client_pjax',
    'enablePushState' => false,
    'enableReplaceState' => false,
    'timeout' => 3000
]);
?>
<?php $activeForm = ActiveForm::begin([
    'method' => 'post',
    'options' => [
        'data-pjax' => 1,
        'class' => 'panel-body',
    ],
    'id' => 'voluntary_refund_preview_email_form',
    'enableClientValidation' => false
]);

echo $activeForm->errorSummary($form);
?>

<?= $activeForm->field($form, 'caseId')->hiddenInput()->label(false) ?>
<?= $activeForm->field($form, 'originProductQuoteId')->hiddenInput()->label(false) ?>
<?= $activeForm->field($form, 'productQuoteRefundId')->hiddenInput()->label(false) ?>
    <div class="row">
        <div class="col-sm-12 form-group">
            <?= $activeForm->field($form, 'clientEmail')->dropDownList($clientEmails) ?>
        </div>
    </div>

    <div class="btn-wrapper text-right">
        <?= Html::button('<i class="fa fa-close"></i> Cancel', ['class' => 'btn btn-sm btn-danger', 'data-dismiss' => 'modal']) ?>
        <?= Html::submitButton('<i class="fa fa-envelope-o"></i> Preview Email', ['class' => 'btn btn-sm btn-success', 'id' => 'voluntary-refund-preview-email-btn']) ?>
    </div>
<?php ActiveForm::end(); ?>

<?php Pjax::end() ?>

<?php
$js = <<<JS
$("#voluntary_refund_choose_client_pjax").on("pjax:start", function() {
    $('#voluntary-refund-preview-email-btn').find('i').replaceWith('<i class="fa fa-spin fa-spinner"></i>');
    $('#voluntary-refund-preview-email-btn').addClass('disabled').prop('disabled', true);
});
JS;
$this->registerJs($js);
