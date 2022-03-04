<?php

/**
 * @var $form ActiveForm
 * @var $this View
 * @var $leadQuoteExtraMarkUpForm LeadQuoteExtraMarkUpForm
 * @var $paxCode string
 * @var $quote Quote
 * @var $clientCurrency Currency
 *
 */

use common\models\Currency;
use common\models\Quote;
use src\forms\lead\LeadQuoteExtraMarkUpForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$defaultCurrencyCode = Currency::getDefaultCurrencyCode();
$inverseCurrencyRate = 1 / $quote->q_client_currency_rate;

?>
    <div class="edit-name-modal-content-ghj">
        <?php $form = ActiveForm::begin([
            'id' => 'lead-quote-extra-mark-up-edit-form',
            'action' => Url::to(['lead-view/ajax-edit-lead-quote-extra-mark-up', 'quoteId' => $quote->id, 'paxCode' => $paxCode]),
            'enableClientValidation' => true,
            'enableAjaxValidation' => false,
            'validateOnChange' => false,
            'validateOnBlur' => false,
        ]);?>
        <?= $form->errorSummary($leadQuoteExtraMarkUpForm) ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field(
                    $leadQuoteExtraMarkUpForm,
                    'qp_client_extra_mark_up',
                    ['inputOptions' => ['id' => 'qp_client_extra_mark_up_modal_field'],]
                )
                         ->input(
                             'number',
                             [
                                 'min'   => 0,
                                 'step'  => 'any',
                                 'value' => (float)$leadQuoteExtraMarkUpForm->qp_client_extra_mark_up
                             ]
                         ) ?>
            </div>
            <div class="col-md-6
            <?php if ($clientCurrency->cur_code == $defaultCurrencyCode) :?>
                        d-none
            <?php endif; ?>">
                <?= $form->field(
                    $leadQuoteExtraMarkUpForm,
                    'extra_mark_up',
                    ['inputOptions' => ['id' => 'extra_mark_up_modal_field']]
                )
                         ->input(
                             'number',
                             ['min' => 0, 'step' => 'any', 'value' => (float)$leadQuoteExtraMarkUpForm->extra_mark_up]
                         ) ?>
            </div>
        </div>
<?php if ($clientCurrency->cur_code !== $defaultCurrencyCode) :?>
        <h2> currency rates:</h2>
    <div class="row">
        <div class="col-md-6">
        <?= Yii::$app
            ->formatter
            ->asCurrenciesComparison($defaultCurrencyCode, $leadQuoteExtraMarkUpForm->clientCurrencyCode, $quote->q_client_currency_rate) ;?>
        </div>
    <div class="col-md-6">
        <?= Yii::$app
            ->formatter
            ->asCurrenciesComparison($leadQuoteExtraMarkUpForm->clientCurrencyCode, $defaultCurrencyCode, $inverseCurrencyRate) ;?>
    </div>
    </div>
<?php endif; ?>


        <div style="margin-top: 20px;" class="text-center">
            <?= Html::submitButton('<i class="fa fa-check-square-o"></i> Update Extra Mark-Up', [
                'class' => 'btn btn-warning'
            ]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

<?php
$js = <<<JS

$('#qp_client_extra_mark_up_modal_field').on('change keyup input',function(){
    let currencyRate = '$inverseCurrencyRate';
    $('#extra_mark_up_modal_field').val(($(this).val() * currencyRate).toFixed(4) );
});
$('#extra_mark_up_modal_field').on('change keyup input',function(){
       let currencyRate = '$quote->q_client_currency_rate';
       $('#qp_client_extra_mark_up_modal_field').val(($(this).val() * currencyRate).toFixed(4));
});

$('#lead-quote-extra-mark-up-edit-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            var type = 'error',
                text = data.message,
                title = 'Lead extra mark-up savin error error';
       
            if (!data.error) {
                $('#modal-client-manage-info').modal('hide');
                
                type = 'success';
                title = 'Quote Extra-mark successfully updated';
                
                $.pjax.reload({container: '#pjax-quote_prices-{$quote->id}', async: false});
                $.pjax.reload({container: '#pjax-quote_estimation_profit-{$quote->id}', async: false});
            }
            
            new PNotify({
                title: title,
                text: data.message,
                type: type
            });
       },
       error: function (error) {
            new PNotify({
                title: 'Error',
                text: 'Internal Server Error. Try again letter.',
                type: 'error'                
            });
       }
    })
    return false;
}); 
JS;
$this->registerJs($js);
?>