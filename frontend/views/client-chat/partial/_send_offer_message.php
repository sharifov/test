<?php

use modules\offer\src\entities\offer\Offer;
use yii\helpers\Html;

/**
 * @var Offer[] $offers
 */
?>
<?php foreach ($offers as $offer) : ?>
<p>Offer: <b><?= $offer->of_name ?></b></p>
<p>Total Price: <b><?= trim(number_format($offer->offerTotalCalcSum * $offer->of_client_currency_rate, 2) . ' ' . ($offer->ofClientCurrency->cur_symbol ?? '')) ?></b></p>
<p>
<a href="<?= $offer->getCheckoutUrlPage() ?>">View Offer</a>
</p>
<br>
<?php endforeach; ?>
