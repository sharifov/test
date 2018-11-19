<?php
/**
 * @var $quotes Quote[]
 * @var $lead Lead
 * @var $leadForm LeadForm
 */

use common\models\Quote;
use common\models\Lead;
use yii\bootstrap\Html;
use yii\helpers\Url;
use common\models\Airline;
use frontend\models\LeadForm;

$extraPriceUrl = \yii\helpers\Url::to(['quote/extra-price']);
$declineUrl = \yii\helpers\Url::to(['quote/decline']);
$statusLogUrl = \yii\helpers\Url::to(['quote/status-log']);
$previewEmailUrl = \yii\helpers\Url::to(['quote/preview-send-quotes']);
$leadId = $lead->id;

$appliedQuote = $lead->getAppliedAlternativeQuotes();

if ($leadForm->mode != $leadForm::VIEW_MODE) {
    $js = <<<JS
    $('[data-toggle="tooltip"]').tooltip();
    $(document).on('click', '.send-quotes-to-email', function () {
        var urlModel = $(this).data('url');
        var email = $('#send-to-email').val();
        var quotes = Array();
        $('.quotes-uid:checked').each(function(idx, elm){
            quotes.push($(elm).val());
        });
        if (quotes.length == 0) {
            return null;
        }
        $('#btn-send-quotes').popover('hide');
        $('#preloader').removeClass('hidden');
        var dataPost = {leadId: $leadId, email:email, quotes: quotes };
        $.ajax({
            url: urlModel,
            type: 'post',
            data: dataPost,
            success: function (data) {
                var editBlock = $('#preview-send-quotes');
                editBlock.find('.modal-body').html(data);
                editBlock.modal('show');

                $('#preloader').addClass('hidden');
            },
            error: function (error) {
                $('#preloader').addClass('hidden');
                console.log('Error: ' + error);
            }
        });
    });

    $('#btn-send-quotes').popover({
        html: true,
        placement: 'top',
        content: function () {
            $('#send-to-email').html('');
            $('.email').each(function(idx, elm){
                var val = $(elm).val();
                if(val != ''){
                    $('#send-to-email').append('<option value="'+val+'">'+val+'</option>');
                }
            });
            return $(".js-pop-emails-content").html();
        }
    });
    $('#lg-btn-send-quotes').click(function() {
        $('#btn-send-quotes').trigger('click');
    });

    $('.ext-mark-up').keyup(function (event) {
        var key = event.keyCode ? event.keyCode : event.which;
        validatePriceField($(this), key);
    });
    $('.ext-mark-up').change(function (event) {
        if ($(this).val().length == 0) {
            $(this).val(0);
        }
        var element = $(this);
        $.ajax({
            type: 'post',
            url: '$extraPriceUrl',
            data: {'quote_uid': $(this).data('quote-uid'), 'value': $(this).val(), 'pax_type': $(this).data('pax-type')},
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    var sell = element.parent().parent().find('.sellingPrice-'+data.uid),
                            totalSell = $('#'+data.uid).find('.total-sellingPrice-'+data.uid),
                            totalMarkup = $('#'+data.uid).find('.total-markup-'+data.uid);

                        sell.text(data.actual.sellingPrice);
                        totalSell.text(data.total.sellingPrice);
                        totalMarkup.text(data.total.markup);

                        $('#isChangedMarkup-'+data.uid).removeClass('hidden');
                    }
            },
            error: function (error) {
            console.log('Error: ' + error);
            }
        });
    });

    $('.view-status-log').click(function(e){
        e.preventDefault();
        $('#preloader').removeClass('hidden');
        var editBlock = $('#get-quote-status-log');
        editBlock.find('.modal-body').html('');
        var id = $(this).attr('data-id');
        editBlock.find('.modal-body').load('$statusLogUrl?quoteId='+id, function( response, status, xhr ) {
            $('#preloader').addClass('hidden');
            editBlock.modal('show');
        });
    });

    $('#btn-declined-quotes').click(function() {
        var quotes = Array();
        $('.quotes-uid:checked').each(function(idx, elm){
            quotes.push($(elm).val());
        });
        if (quotes.length == 0) {
            return null;
        }
        var dataPost = {quotes: quotes};
        $('#preloader').removeClass('hidden');
        $.ajax({
            type: 'post',
            url: '$declineUrl',
            data: dataPost,
            success: function (data) {
                $('#preloader').addClass('hidden');
                if (data.errors.length != 0) {
                    $('#sent-messages').removeClass('hidden').addClass('alert-danger').removeClass('alert-success');
                    $('#sent-messages').find('.fa-exclamation-triangle').addClass('hidden');
                    $('#sent-messages').find('.fa-times-circle').removeClass('hidden');
                    var errors = Array();
                    $.each(data.errors, function(i, elm){errors.push(elm)});
                    $('#sent-messages').find('div').html(errors.join('<br>'));
                    $('#sent-messages').show();
                    $('#sent-messages').fadeOut(10000);
                } else {
                    $('#sent-messages').removeClass('hidden').addClass('alert-success').removeClass('alert-danger');
                    $('#sent-messages').find('.fa-exclamation-triangle').removeClass('hidden');
                    $('#sent-messages').find('.fa-times-circle').addClass('hidden');
                    $('#sent-messages').find('div').html(quotes.length+' quotes was successfully Declined');
                    $('#sent-messages').show();
                    $('#sent-messages').fadeOut(10000);

                    $.each(quotes, function(idx, quote) {
                        $('#q-status-'+quote).text('Declined');
                        $('#q-status-'+quote).attr('class', 'sl-quote__status status-label label label-danger');
                    });

                    $.each($('.quotes-uid:checked'), function(idx, elm) {
                        elm.parentElement.classList.add('hidden');
                    });
                }
            },
            error: function (error) {
                $('#preloader').addClass('hidden');
                console.log('Error: ' + error);
            }
        });
    });
JS;
    $this->registerJs($js);
}
?>

<?php if ($leadForm->mode != $leadForm::VIEW_MODE) : ?>
    <div class="btn-wrapper pt-20 mb-20">
        <?= Html::button('<i class="fa fa-eye-slash"></i>&nbsp;Declined Quotes', [
            'class' => 'btn btn-primary btn-lg',
            'id' => 'btn-declined-quotes',
        ]) ?>
        <!--Button Send-->
        <span class="btn-group">
            <?= Html::button('<i class="fa fa-send"></i>&nbsp;Send Quotes', [
                'class' => 'btn btn-lg btn-success',
                'id' => 'lg-btn-send-quotes',
            ]) ?>
            <?= Html::button('<span class="caret"></span>', [
                'id' => 'btn-send-quotes',
                'class' => 'btn btn-lg btn-success dropdown-toggle sl-popover-btn',
                'data-toggle' => 'popover',
                'title' => '',
                'data-original-title' => 'Select Emails',
            ]) ?>
        </span>
        <div class="hidden js-pop-emails-content sl-popover-emails">
            <label for="send-to-email" class="select-wrap-label mb-20" style="width:250px;">
                <?= Html::dropDownList('send_to_email', null, [], [
                    'class' => 'form-control',
                    'id' => 'send-to-email'
                ]) ?>
            </label>
            <div>
                <?= Html::button('Send', [
                    'class' => 'btn btn-success send-quotes-to-email',
                    'id' => 'btn-send-quotes-email',
                    'data-url' => \yii\helpers\Url::to(['quote/preview-send-quotes'])
                ]) ?>
            </div>
        </div>
    </div>
    <div id="sent-messages" class="alert hidden">
        <i class="fa fa-exclamation-triangle hidden"></i>
        <i class="fa fa-times-circle hidden"></i>
        <div></div>
    </div>
<?php endif; ?>
<?php foreach ($quotes as $key => $quote):
    $collapsed = false;
    $tagACollapseClass = '';
    if (!empty($appliedQuote)) {
        $collapsed = true;
        $tagACollapseClass = 'collapsed';
    }
    if ($quote->status == $quote::STATUS_APPLIED) {
        $collapsed = false;
        $tagACollapseClass = '';
    } else if ($quote->status == $quote::STATUS_DECLINED) {
        $collapsed = true;
        $tagACollapseClass = 'collapsed';
    }
    ?>
    <div class="panel panel-primary panel-wrapper sl-quote" id="<?= $quote->uid ?>">
        <div class="panel-heading collapsing-heading">
            <a data-toggle="collapse" href="#quote-<?= $quote->uid ?>"
               class="collapsing-heading__collapse-link <?= $tagACollapseClass ?>">
                <?= sprintf('%d. #%s Quote | Cabin Class: %s',
                    (count($quotes) - $key),
                    $quote->uid,
                    Lead::getCabin($quote->cabin)
                ) ?>
                | <span>Creator: <?= $quote->employee_name ?> (<?= ($quote->created_by_seller) ? 'Agent' : 'Expert'?>)</span>
                <?= $quote->getStatusLabel() ?>
            </a>
            <?= Html::a('<i class="fa fa-history"></i>', '#', [
                'style' => 'color: #ffffff;',
                'class' => 'view-status-log sl-quote__status-log btn btn-info btn-sm',
                'data-id' => $quote->id,
                'title' => 'View status log'
            ]) ?>
            <?php if ($lead->getAppliedAlternativeQuotes() === null) {
                echo Html::button('<i class="fa fa-copy"></i>', [
                    'class' => 'btn btn-primary btn-sm sl-quote__clone add-clone-alt-quote',
                    'data-uid' => $quote->uid,
                    'data-url' => Url::to(['quote/clone', 'leadId' => $lead->id, 'qId' => $quote->id])
                ]);
            } ?>
            <?php if ($leadForm->mode != $leadForm::VIEW_MODE && in_array($quote->status, [$quote::STATUS_CREATED, $quote::STATUS_SEND])) : ?>
                <div class="custom-checkbox sl-quote__check">
                    <input class="quotes-uid" id="q<?= $quote->uid ?>" value="<?= $quote->uid ?>"
                           type="checkbox" name="quote[<?= $quote->uid ?>]">
                    <label for="q<?= $quote->uid ?>"></label>
                </div>
            <?php endif; ?>
        </div>
        <div class="panel-body collapse <?= ($collapsed) ? '' : 'in' ?>"
             id="quote-<?= $quote->uid ?>">
            <div class="sl-quote__content">
                <div class="sl-quote__pricing">
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="sl-quote__subtitle">Pricing</h5>
                        </div>
                        <div class="col-ms-8 hidden" id="isChangedMarkup-<?= $quote->uid ?>">
                            <span class="text-danger">The price has changed</span>
                        </div>
                    </div>
                    <?php
                    $now = new \DateTime();
                    $adultsPrices = $childrenPrices = $infantsPrices = [
                        'cnt' => 0, 'net' => 0,
                        'sell' => 0, 'markup' => 0, 'saleMarkUp' => 0
                    ];
                    $prices = $quote->quotePrices;
                    $netPrice = $sellingPrice = $markup = $extraMarkup = 0;
                    foreach ($prices as $idx => $price) {
                        $price->toFloat();
                        $netPrice += $price->net;
                        $markup += $price->mark_up;
                        $sellingPrice += $price->selling;
                        $extraMarkup += $price->extra_mark_up;
                        switch ($price->passenger_type) {
                            case $price::PASSENGER_CHILD:
                                $childrenPrices['cnt']++;
                                $childrenPrices['net'] += $price->net;
                                $childrenPrices['sell'] += $price->selling;
                                $childrenPrices['markup'] += $price->mark_up;
                                $childrenPrices['saleMarkUp'] += $price->extra_mark_up;
                                break;
                            case $price::PASSENGER_INFANT:
                                $infantsPrices['cnt']++;
                                $infantsPrices['net'] += $price->net;
                                $infantsPrices['sell'] += $price->selling;
                                $infantsPrices['markup'] += $price->mark_up;
                                $infantsPrices['saleMarkUp'] += $price->extra_mark_up;
                                break;
                            default:
                                $adultsPrices['cnt']++;
                                $adultsPrices['net'] += $price->net;
                                $adultsPrices['sell'] += $price->selling;
                                $adultsPrices['markup'] += $price->mark_up;
                                $adultsPrices['saleMarkUp'] += $price->extra_mark_up;
                                break;
                        }
                    }
                    ?>
                    <table class="table table-neutral">
                        <thead>
                        <tr>
                            <td></td>
                            <td>Qty</td>
                            <td style="min-width: 80px;">Net Pr.</td>
                            <td style="min-width: 80px;">Markup</td>
                            <td>Extra Markup</td>
                            <td style="min-width: 80px;">Selling Pr.</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($adultsPrices['cnt'] > 0): ?>
                            <tr>
                                <th style="vertical-align: inherit;">Adults</th>
                                <td style="vertical-align: inherit;"><?= $adultsPrices['cnt'] ?></td>
                                <td style="vertical-align: inherit;"><?= $adultsPrices['net'] / $adultsPrices['cnt'] ?>
                                    $
                                </td>
                                <td style="vertical-align: inherit;"><?= $adultsPrices['markup'] / $adultsPrices['cnt'] ?>
                                    $
                                </td>
                                <td class="input-group">
                                    <?= Html::textInput('adt-markup-' . $quote->uid, $adultsPrices['saleMarkUp'] / $adultsPrices['cnt'], [
                                        'class' => 'form-control ext-mark-up',
                                        'data-quote-uid' => $quote->uid,
                                        'data-pax-type' => 'adt-markup'
                                    ]) ?><span class="input-group-addon">$</span>
                                </td>
                                <td style="vertical-align: inherit;">
                                    <span class="sellingPrice-<?= $quote->uid ?>"><?= $adultsPrices['sell'] / $adultsPrices['cnt'] ?></span>$
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($childrenPrices['cnt'] > 0): ?>
                            <tr>
                                <th style="vertical-align: inherit;">Children</th>
                                <td style="vertical-align: inherit;"><?= $childrenPrices['cnt'] ?></td>
                                <td style="vertical-align: inherit;"><?= $childrenPrices['net'] / $childrenPrices['cnt'] ?>
                                    $
                                </td>
                                <td style="vertical-align: inherit;"><?= $childrenPrices['markup'] / $childrenPrices['cnt'] ?>
                                    $
                                </td>
                                <td class="input-group">
                                    <?= Html::textInput('cnn-markup-' . $quote->uid, $childrenPrices['saleMarkUp'] / $childrenPrices['cnt'], [
                                        'class' => 'form-control ext-mark-up',
                                        'data-quote-uid' => $quote->uid,
                                        'data-pax-type' => 'cnn-markup'
                                    ]) ?><span class="input-group-addon">$</span>
                                </td>
                                <td style="vertical-align: inherit;">
                                    <span class="sellingPrice-<?= $quote->uid ?>"><?= $childrenPrices['sell'] / $childrenPrices['cnt'] ?></span>$
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($infantsPrices['cnt'] > 0): ?>
                            <tr>
                                <th style="vertical-align: inherit;">Infants</th>
                                <td style="vertical-align: inherit;"><?= $infantsPrices['cnt'] ?></td>
                                <td style="vertical-align: inherit;"><?= $infantsPrices['net'] / $infantsPrices['cnt'] ?>
                                    $
                                </td>
                                <td style="vertical-align: inherit;"><?= $infantsPrices['markup'] / $infantsPrices['cnt'] ?>
                                    $
                                </td>
                                <td class="input-group">
                                    <?= Html::textInput('inf-markup-' . $quote->uid, $infantsPrices['saleMarkUp'] / $infantsPrices['cnt'], [
                                        'class' => 'form-control ext-mark-up',
                                        'data-quote-uid' => $quote->uid,
                                        'data-pax-type' => 'inf-markup'
                                    ]) ?><span class="input-group-addon">$</span>
                                </td>
                                <td style="vertical-align: inherit;">
                                    <span class="sellingPrice-<?= $quote->uid ?>"><?= $infantsPrices['sell'] / $infantsPrices['cnt'] ?></span>$
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                        <tr class="text-bold bg-info">
                            <th>Total</th>
                            <td><?= count($quote->quotePrices) ?></td>
                            <td><?= $netPrice ?>$</td>
                            <td><?= $markup ?>$</td>
                            <td><span class="total-markup-<?= $quote->uid ?>"><?= $extraMarkup ?></span>$
                            </td>
                            <td>
                                <span class="total-sellingPrice-<?= $quote->uid ?>"><?= $sellingPrice ?></span>$
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="sl-quote__central">
                    <div class="sl-quote__attributes">
                        <strong>Validating Carrier: </strong><?php
                        $airline = Airline::findIdentity($quote->main_airline_code);
                        echo sprintf('%s', $airline->name);
                        ?> <span class="badge badge-info"><?= $airline->iata ?></span>
                    </div>
                    <div class="sl-quote__dump">
                        <h5 class="sl-quote__subtitle">Reservation dump</h5>
                        <textarea readonly class="box-bordered" rows="5"
                                  style="width:100%;"><?= $quote->reservation_dump ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>



<div class="modal modal-quote fade" id="preview-send-quotes" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Preview email
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="max-height: calc(100vh - 212px); overflow-y: auto;">
            </div>
        </div>
    </div>
</div>
