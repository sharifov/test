<?php

/**
 * @var $form ActiveForm
 * @var $this View
 * @var $leadQuoteExtraMarkUpForm LeadQuoteExtraMarkUpForm
 * @var $paxCode string
 * @var $quote Quote
 *
 */

use common\models\Currency;
use common\models\Quote;
use src\forms\lead\LeadQuoteExtraMarkUpForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$defaultCurrencyCode     = Currency::getDefaultCurrencyCode();
$inverseCurrencyRate     = 1 / $quote->q_client_currency_rate;
$qp_client_extra_mark_up = (float)$leadQuoteExtraMarkUpForm->qp_client_extra_mark_up;
$extra_mark_up           = (float)$leadQuoteExtraMarkUpForm->extra_mark_up;

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
            <div class="col-md-12">
                <?= $form->field(
            $leadQuoteExtraMarkUpForm,
            'qp_client_extra_mark_up',
            [
                        'template' => '{label} <div class="input-group">{input}<span class="input-group-addon">' . $quote->q_client_currency . '</span></div>{error}{hint}',
                            'inputOptions' => ['id' => 'qp_client_extra_mark_up_modal_field'],
                        ]
        )
                         ->input(
                             'number',
                             [
                                 'min'   => 0,
                                 'step'  => '0.01',
                                 'value' => $qp_client_extra_mark_up
                             ]
                         )
                         ->label('Client Currency ' . ' (' . $quote->q_client_currency . ')')
                ?>
                <?php if ($quote->q_client_currency !== $defaultCurrencyCode) :?>
                    <div class="label label-default" title="Rate: <?= Html::decode(round($quote->q_client_currency_rate, 5))?>
                    <?= Html::decode($quote->q_client_currency)?>">
                        1 <?=$defaultCurrencyCode?> = <?= round($quote->q_client_currency_rate, 4)?>
                        <?=Html::decode($quote->q_client_currency); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-12">
                <hr>
            </div>

            <div class="col-md-12
            <?php if ($quote->q_client_currency == $defaultCurrencyCode) : ?>
                        d-none
            <?php endif; ?>">


                <?= $form->field(
                            $leadQuoteExtraMarkUpForm,
                            'extra_mark_up',
                            [
                        'template' => '{label} <div class="input-group">{input}<span class="input-group-addon">' . $defaultCurrencyCode . '</span></div>{error}{hint}',
                        'inputOptions' => ['id' => 'extra_mark_up_modal_field']
                    ]
                        )
                         ->input(
                             'number',
                             ['min' => 0,  'step'  => '0.01', 'value' => $extra_mark_up]
                         )
                         ->label('Base Currency ' . ' (' . $defaultCurrencyCode . ')')
                ?>

                <?php if ($quote->q_client_currency !== $defaultCurrencyCode) :?>
                    <span class="label label-default" title="Rate: <?= Html::decode(round($inverseCurrencyRate, 5))?>
                        <?= Html::decode($defaultCurrencyCode)?>">
                            1 <?= $quote->q_client_currency?>
                        = <?=round($inverseCurrencyRate, 4)?> <?= Html::decode($defaultCurrencyCode)?>
                    </span>
                <?php endif; ?>

            </div>
        </div>


        <div style="margin-top: 20px;" class="text-center">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save Extra MarkUp', [
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

<?php
$js = <<<JS

$('#qp_client_extra_mark_up_modal_field').on('change keyup input',function(){
    let currencyRate = '$inverseCurrencyRate';
    $('#extra_mark_up_modal_field').val(($(this).val() * currencyRate).toFixed(2) );
});
$('#extra_mark_up_modal_field').on('change keyup input',function(){
       let currencyRate = '$quote->q_client_currency_rate';
       $('#qp_client_extra_mark_up_modal_field').val(($(this).val() * currencyRate).toFixed(2));
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
                title = 'Lead extra markup savin error error';
       
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