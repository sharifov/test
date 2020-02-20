<?php
/**
 * @var $this View
 * @var $productQuote ProductQuote
 */

use modules\product\src\entities\productQuote\ProductQuote;
use yii\helpers\Html;
use yii\web\View;

$totalAmountOption = 0;
$totalClientAmountOption = 0;
$totalExtraMarkupOption = 0;
?>

<hr>
<div class="text-right">
    <h4>
        <span class="<?=$productQuote->pq_profit_amount < 0 ? 'danger' : ''?>" title="Profit amount: <?=number_format($productQuote->pq_profit_amount, 2)?> $" data-toggle="tooltip">
            Client Total: <b><?=number_format($productQuote->pq_client_price, 2)?> <?= Html::encode($productQuote->pq_client_currency)?></b>
        (<?=number_format($productQuote->pq_price, 2)?> USD)
        </span>
    </h4>
</div>
