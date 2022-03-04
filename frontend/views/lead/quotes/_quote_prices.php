<?php

/**
 * @var $this \yii\web\View
 * @var $quote \common\models\Quote
 */

use common\models\Currency;
use kartik\editable\Editable;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\model\quote\abac\dto\QuoteExtraMarkUpChangeAbacDto;
use src\model\quote\abac\QuoteExtraMarkUpChangeAbacObject;
use src\services\quote\quotePriceService\ClientQuotePriceService;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php
$currency = empty($quote->q_client_currency) ? Currency::getDefaultCurrencyCode() : $quote->q_client_currency;
$quoteExtraMarkUpAbacDto = new QuoteExtraMarkUpChangeAbacDto($quote->lead, Auth::id(), $quote);
/** @abac quoteExtraMarkUpAbacDto, QuoteExtraMarkUpChangeAbacObject::QUOTE_CHANGE_EXTRA_MARK_UP_FORM, QuoteExtraMarkUpChangeAbacObject::ACTION_EDIT, Access to edit Quote Extra mark-up */
$canEditQuoteExtraMarkUp = Yii::$app->abac->can(
    $quoteExtraMarkUpAbacDto,
    QuoteExtraMarkUpChangeAbacObject::QUOTE_CHANGE_EXTRA_MARK_UP_FORM,
    QuoteExtraMarkUpChangeAbacObject::ACTION_EDIT
);

if ($quote->isClientCurrencyDefault()) {
    $priceData = $quote->getPricesData();
} else {
    try {
        $priceData = (new ClientQuotePriceService($quote))->getClientPricesData();
    } catch (\Throwable $throwable) {
        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['quoteId' => $quote->id]);
        \Yii::error($message, 'QuotePrices:PriceData:Throwable');
        $priceData = $quote->getPricesData();
        $currency = Currency::getDefaultCurrencyCode();
    }
}
?>
<table class="table table-striped table-prices" id="quote-prices-<?= $quote->id?>">
    <thead>
        <tr>
            <th>Pax</th>
            <th>Q</th>
            <th>NP, <?php echo $currency ?></th>
            <th>Mkp, <?php echo $currency ?></th>
            <th>Ex Mkp, <?php echo $currency ?></th>
            <th>SP, <?php echo $currency ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($priceData['prices'] as $paxCode => $price) :?>
        <tr>
            <th><?= $paxCode?></th>
            <td>x <?= $price['tickets']?></td>
            <td><?= number_format($price['net'] / $price['tickets'], 2) ?></td>
            <td><?= number_format($price['mark_up'] / $price['tickets'], 2) ?></td>
            <td><?php if ($canEditQuoteExtraMarkUp) :?>
                    <?=
                    Html::a('<i class="fa fa-edit yellow"></i>' . number_format($price['extra_mark_up'] / $price['tickets'], 2), '#', [
                        'class' => 'showModalButton',
                        'title' =>  'Edit extra mark-up, QUID:' . $quote->uid,
                        'data-modal_id' => 'client-manage-info',
                        'data-content-url' => Url::to([
                            'lead-view/ajax-edit-lead-quote-extra-mark-up-modal-content',
                            'quoteId' => $quote->id,
                            'paxCode' =>  strtolower($paxCode)
                        ])
                    ])
                    ?>
                <?php else :?>
                    <?= number_format($price['extra_mark_up'] / $price['tickets'], 2)?>
                <?php endif;?>
            </td>
            <td><?= number_format($price['selling'] / $price['tickets'], 2) ?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <td><?= $priceData['total']['tickets']?></td>
            <td><?= number_format($priceData['total']['net'], 2)?></td>
            <td><?= number_format($priceData['total']['mark_up'], 2)?></td>
            <td class="total-markup-<?= $quote->uid ?>"><?= number_format($priceData['total']['extra_mark_up'], 2)?></td>
            <td class="total-sellingPrice-<?= $quote->uid ?>"><?= number_format($priceData['total']['selling'], 2)?></td>
        </tr>
    </tfoot>
</table>