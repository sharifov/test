<?php
/**
 * @var $this \yii\web\View
 * @var $quote \common\models\Quote
 */
?>
<?php $priceData = $quote->getPricesData();?>
<table class="table table-striped table-prices" id="quote-prices-<?= $quote->id?>">
    <thead>
        <tr>
            <th>Pax</th>
            <th>Q</th>
            <th>NP, $</th>
            <th>Mkp, $</th>
            <th>Ex Mkp, $</th>
            <th>SP, $</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($priceData['prices'] as $paxCode => $price) :?>
        <tr>
            <th><?= $paxCode?></th>
            <td>x <?= $price['tickets']?></td>
            <td><?= number_format($price['net'] / $price['tickets'], 2) ?></td>
            <td><?= number_format($price['mark_up'] / $price['tickets'], 2) ?></td>
            <td><?= number_format($price['extra_mark_up'] / $price['tickets'], 2)?></td>
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