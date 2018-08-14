<?php

/**
 * @var $lead Lead
 * @var $this \yii\web\View
 * @var $alternativeQuotes array
 */

use yii\helpers\Html;
use common\models\Lead;
$formID = 'getOnlineQuotes';

$js = <<<JS
    $('.create-new-quote').click(function (e) {
        e.preventDefault();
        var quoteBlock = $('#create-quote');
        quoteBlock.find('.modal-body').html('');
    
        $('#itinerary-key-id').val($(this).data('key'));
        var form = $('#$formID');
        $('#preloader').removeClass('hidden');
        $('#quick-search').modal('hide');
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: form.serialize(),
            success: function (data) {
                $('#preloader').addClass('hidden');
                quoteBlock.find('.modal-body').html(data.body)
                $('#cancel-alt-quote').attr('data-type', 'search');
                quoteBlock.modal({
                  backdrop: 'static',
                  show: true
                });
            },
            error: function (error) {	
                $('#quick-search').modal('show');
                console.log('Error: ' + error);			
            }
        });
    });
JS;

$this->registerJs($js);
?>
<div>
    <?php foreach ($alternativeQuotes as $key => $alternativeQuote): ?>
        <div class="panel panel-info panel-wrapper sl-quote" id="<?= $alternativeQuote['key'] ?>">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-5">
                        <?= sprintf('%d. | PCC: %s | Seats: %d | Duration: %s',
                            $key + 1,
                            $alternativeQuote['pcc'],
                            $alternativeQuote['maxSeats'],
                            \common\models\Quote::getElapsedTime($alternativeQuote['duration'])
                        ) ?>
                    </div>
                    <div class="col-md-offset-5 col-md-2 text-right">
                        <?= Html::button('Created', [
                            'class' => 'btn btn-success create-new-quote',
                            'data-key' => $alternativeQuote['key']
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="sl-quote__content">
                    <div class="sl-quote__pricing">
                        <div class="row">
                            <div class="col-md-4">
                                <h5 class="sl-quote__subtitle">Pricing</h5>
                            </div>
                        </div>
                        <?php
                        $now = new \DateTime();
                        $adultsPrices = $childrenPrices = $infantsPrices = [
                            'cnt' => 0, 'net' => 0, 'base' => 0, 'taxes' => 0
                        ];
                        $netPrice = $basePrice = $taxes = 0;
                        if (!empty($lead->adults)) {
                            $adultsPrices['cnt'] = $lead->adults;
                            $adultsPrices['net'] = $adultsPrices['cnt'] * ($alternativeQuote['adultBasePrice'] + $alternativeQuote['adultTax']);
                            $adultsPrices['base'] = $adultsPrices['cnt'] * $alternativeQuote['adultBasePrice'];
                            $adultsPrices['taxes'] = $adultsPrices['cnt'] * $alternativeQuote['adultTax'];
                            $netPrice += $adultsPrices['net'];
                            $basePrice += $adultsPrices['base'];
                            $taxes += $adultsPrices['taxes'];
                        }
                        if (!empty($lead->children)) {
                            $childrenPrices['cnt'] = $lead->children;
                            $childrenPrices['net'] = $childrenPrices['cnt'] * ($alternativeQuote['childBasePrice'] + $alternativeQuote['childTax']);
                            $childrenPrices['base'] = $childrenPrices['cnt'] * $alternativeQuote['childBasePrice'];
                            $childrenPrices['taxes'] = $childrenPrices['cnt'] * $alternativeQuote['childTax'];
                            $netPrice += $childrenPrices['net'];
                            $basePrice += $childrenPrices['base'];
                            $taxes += $childrenPrices['taxes'];
                        }
                        ?>
                        <table class="table table-neutral">
                            <thead>
                            <tr>
                                <td></td>
                                <td>Qty</td>
                                <td style="min-width: 80px;">Base Pr.</td>
                                <td style="min-width: 80px;">Taxes</td>
                                <td style="min-width: 80px;">Net Pr.</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($adultsPrices['cnt'] > 0): ?>
                                <tr>
                                    <th style="vertical-align: inherit;">Adults</th>
                                    <td style="vertical-align: inherit;"><?= $adultsPrices['cnt'] ?></td>
                                    <td style="vertical-align: inherit;"><?= $adultsPrices['base'] / $adultsPrices['cnt'] ?>
                                        $
                                    </td>
                                    <td style="vertical-align: inherit;"><?= $adultsPrices['taxes'] / $adultsPrices['cnt'] ?>
                                        $
                                    </td>
                                    <td style="vertical-align: inherit;">
                                        <span><?= $adultsPrices['net'] / $adultsPrices['cnt'] ?></span>$
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($childrenPrices['cnt'] > 0): ?>
                                <tr>
                                    <th style="vertical-align: inherit;">Children</th>
                                    <td style="vertical-align: inherit;"><?= $childrenPrices['cnt'] ?></td>
                                    <td style="vertical-align: inherit;"><?= $childrenPrices['base'] / $childrenPrices['cnt'] ?>
                                        $
                                    </td>
                                    <td style="vertical-align: inherit;"><?= $childrenPrices['taxes'] / $childrenPrices['cnt'] ?>
                                        $
                                    </td>
                                    <td style="vertical-align: inherit;">
                                        <span><?= $childrenPrices['net'] / $childrenPrices['cnt'] ?></span>$
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
                                    <td style="vertical-align: inherit;">
                                        <span><?= $infantsPrices['sell'] / $infantsPrices['cnt'] ?></span>$
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                            <tr class="text-bold bg-info">
                                <th>Total</th>
                                <td><?= ($adultsPrices['cnt'] + $childrenPrices['cnt'] + $infantsPrices['cnt']) ?></td>
                                <td><?= $basePrice ?>$</td>
                                <td><?= $taxes ?>$</td>
                                <td><?= $netPrice ?>$</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="sl-quote__central">
                        <div class="sl-quote__attributes">
                            <strong>Validating Carrier: </strong><?= $alternativeQuote['mainAirlineName'] ?> <span
                                class="badge badge-info"><?= $alternativeQuote['mainAirlineCode'] ?></span>
                        </div>
                        <div class="sl-quote__dump">
                            <h5 class="sl-quote__subtitle">Reservation dump</h5>
                            <textarea readonly class="box-bordered" rows="5"
                                      style="width:100%;"><?= \common\components\GTTGlobal::getItineraryDump($alternativeQuote['trips']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
