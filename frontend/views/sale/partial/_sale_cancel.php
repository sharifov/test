<?php

use common\components\bootstrap4\activeForm\ActiveForm;
use modules\cases\src\entities\caseSale\CancelSaleReason;
use yii\helpers\Url;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $caseId integer */
/* @var $caseSaleId integer */

/* @var $caseSaleCancelForm src\forms\caseSale\CaseSaleCancelForm */
/* @var $form yii\widgets\ActiveForm */

$urlCancelSale = Url::to(['/sale/cancel-sale']);
?>

    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin([
                'id' => 'cancel-sale-form',
                'action' => ['sale/cancel-sale'],
                'validateOnChange' => false,
                'validateOnBlur' => false,
                'enableClientValidation' => false,
                'enableAjaxValidation' => true,
            ]); ?>

            <?= $form->errorSummary($caseSaleCancelForm) ?>

            <?= $form->field($caseSaleCancelForm, 'caseId')->hiddenInput()->label(false); ?>
            <?= $form->field($caseSaleCancelForm, 'caseSaleId')->hiddenInput()->label(false); ?>
            <?= $form->field($caseSaleCancelForm, 'reasonId')->dropDownList($caseSaleCancelForm->getReasonList(), ['prompt' => '-']) ?>

            <div class="message-wrapper d-none">
                <?= $form->field($caseSaleCancelForm, 'message')->textarea(['rows' => 3]) ?>
            </div>

            <div class="form-group text-center">
                <?= Html::submitButton('Confirm', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

<?php

$reasonId = Html::getInputId($caseSaleCancelForm, 'reasonId');
$message = Html::getInputId($caseSaleCancelForm, 'message');
$otherReason = CancelSaleReason::OTHER;

$js = <<<JS
$('body').find('#{$reasonId}').on('change', function () {
    var val = $(this).val() || null;
    var messageWrapper = $('.message-wrapper');
    
    if (val == '{$otherReason}') {
        messageWrapper.removeClass('d-none');
    } else {
        $('#{$message}').val('');
        messageWrapper.addClass('d-none');
    }
})
JS;
$this->registerJs($js);
