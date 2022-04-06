<?php

/**
 * @var $this \yii\web\View
 * @var $quote Quote
 * @var $priceData array
 */

use common\models\Currency;
use common\models\query\CurrencyQuery;
use common\models\Quote;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\model\quote\abac\dto\QuoteFlightExtraMarkupAbacDto;
use src\model\quote\abac\QuoteFlightAbacObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php
$currency = empty($quote->q_client_currency) ? Currency::getDefaultCurrencyCode() : $quote->q_client_currency;
$isOwner = $quote->lead->employee_id == Auth::id();
$quoteFlightExtraMarkUpAbacDto = new QuoteFlightExtraMarkupAbacDto($quote->lead, $quote, $isOwner);
/** @abac quoteFlightExtraMarkUpAbacDto, QuoteFlightAbacObject::OBJ_EXTRA_MARKUP, QuoteExtraMarkUpChangeAbacObject::ACTION_UPDATE, Access to edit Quote Extra mark-up */
$canEditQuoteExtraMarkUp = Yii::$app->abac->can(
    $quoteFlightExtraMarkUpAbacDto,
    QuoteFlightAbacObject::OBJ_EXTRA_MARKUP,
    QuoteFlightAbacObject::ACTION_UPDATE
);

try {
    if (!$currencySymbol = CurrencyQuery::getCurrencySymbolByCode($currency)) {
        throw new \RuntimeException('Currency Symbol not found');
    }
} catch (\Throwable $throwable) {
    $message = ArrayHelper::merge(
        AppHelper::throwableLog($throwable),
        ['quoteId' => $quote->id, 'currency' => $currency]
    );
    \Yii::error($message, 'QuotePrices:currencySymbol:Throwable');
    $currencySymbol = $currency;
}

?>

<table class="table table-hover table-bordered" id="quote-prices-<?= $quote->id?>">
    <thead>
        <tr class="text-center table-<?= $quote->isDeclined() ? 'default' : 'primary' ?>">
            <th title="Pax type" data-toggle="tooltip">Pax type</th>
            <th title="Quantity" data-toggle="tooltip">Quan</th>
            <th title="Net price" data-toggle="tooltip">Net Price, <?php echo $currencySymbol ?></th>
            <th title="Markup" data-toggle="tooltip">Markup, <?php echo $currencySymbol ?></th>
            <th title="Extra Markup" data-toggle="tooltip">Ex. Markup, <?php echo $currencySymbol ?></th>
            <th title="Selling price" data-toggle="tooltip">Sel. Price, <?php echo $currencySymbol ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($priceData['prices'] as $paxCode => $price) :?>
        <tr class="text-right">
            <th><i class="fa fa-user"></i> <?= Html::encode($paxCode)?></th>
            <td><i class="fa fa-user"></i> x <?= $price['tickets']?></td>
            <td>
                <?= number_format($price['net'] / $price['tickets'], 2) ?>
                <?= Html::encode($currency)?>
            </td>
            <td>
                <?= number_format($price['mark_up'] / $price['tickets'], 2) ?>
                <?= Html::encode($currency)?>
            </td>
            <td><?php if ($canEditQuoteExtraMarkUp) :?>
                    <u>
                    <?=
                    Html::a(number_format($price['extra_mark_up'] / $price['tickets'], 2), 'javascript:void(0)', [
                        'class' => 'showModalButton',
                        'title' =>  'Edit extra markup, QUID: ' . $quote->uid,
                        'data-modal_id' => 'client-manage-info',
                        'data-content-url' => Url::to([
                            'lead-view/ajax-edit-lead-quote-extra-mark-up-modal-content',
                            'quoteId' => $quote->id,
                            'paxCode' =>  strtolower($paxCode)
                        ])
                    ])
                    ?></u>
                    <?= Html::encode($currency)?>
                <?php else :?>
                    <?= number_format($price['extra_mark_up'] / $price['tickets'], 2)?>
                    <?= Html::encode($currency)?>
                <?php endif;?>
            </td>
            <td>
                <?= number_format($price['selling'] / $price['tickets'], 2) ?>
                <?= Html::encode($currency)?>
            </td>
        </tr>
        <?php endforeach;?>
    </tbody>
    <tfoot>
        <tr class="text-right">
            <th>Total</th>
            <td><?= $priceData['total']['tickets']?></td>
            <td>
                <b><?= number_format($priceData['total']['net'], 2)?></b>
                <?= Html::encode($currency)?>
            </td>
            <td>
                <b><?= number_format($priceData['total']['mark_up'], 2)?></b>
                <?= Html::encode($currency)?>
            </td>
            <td class="total-markup-<?= $quote->uid ?>">
                <b><?= number_format($priceData['total']['extra_mark_up'], 2)?></b>
                <?= Html::encode($currency)?>
            </td>
            <td class="total-sellingPrice-<?= $quote->uid ?>">
                <b><?= number_format($priceData['total']['selling'], 2)?></b>
                <?= Html::encode($currency)?>
            </td>
        </tr>
    </tfoot>
</table>
