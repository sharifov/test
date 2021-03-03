<?php

use modules\offer\src\entities\offer\Offer;

/**
 * @var Offer[] $offers
 */
?>

<?php /* foreach ($offers as $offer): ?>
<template>
    <p>Offer: <b><?= $offer->of_name ?></b></p>
    <p>Total Price: <b><?= $offer->ofClientCurrency->cur_symbol ?? '' ?> <?= $offer->of_client_total ?></b></p>
    <p><a href="<?= $offer->getCheckoutUrlPage() ?>" target="_blank">Checkout Page</a></p>
</template>

<br>
<br>
<?php endforeach; */ ?>

<?php foreach ($offers as $offer) : ?>
Offer: **<?= $offer->of_name ?>**
Total Price: **<?= trim(($offer->ofClientCurrency->cur_symbol ?? '') . $offer->of_client_total) ?>**
[Checkout Page](<?= $offer->getCheckoutUrlPage() ?>)
    ---
<?php endforeach; ?>
