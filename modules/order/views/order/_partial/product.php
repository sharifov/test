<?php

use yii\helpers\Html;
use modules\order\src\entities\order\Order;

/**
 * @var Order $order
 */

$orderRelatedQuotes = $order->productQuotes;
$orderRelatedProducts = [];
$filterProducts = [];

$flightProductQuotes = [];
$hotelProductQuotes = [];


foreach ($orderRelatedQuotes as $productQuote) {
    if (!in_array($productQuote->pq_product_id, $filterProducts)) {
        $orderRelatedProducts[] = $productQuote->pqProduct;
        $filterProducts[] = $productQuote->pq_product_id;
    }
}

?>

<div class="order-view-product-box">
    <div id="pjax-order-invoice-18" data-pjax-container="" data-pjax-timeout="10000">
        <div class="x_panel x_panel_product">
            <div class="x_title">
                <h2><i class="fas fa-file-contract"></i> Product List</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">
                <?php foreach ($orderRelatedProducts as $product) : ?>
                <div class="x_panel">
                    <div class="x_title">
                        <h2>
                            <a class="collapse-link">
                                <i class="<?= Html::encode($product->getIconClass()) ?>" title="ID: <?=$product->pr_id?>"></i> <?=Html::encode($product->prType->pt_name)?> <?=$product->pr_name ? ' - ' . Html::encode($product->pr_name) : ''?>
                            </a>
                            <sup title="Number of quotes">(<?=count($product->productQuotes)?>)</sup>

                            <?php if ($product->pr_description) :?>
                                <a  id="product_description_<?=$product->pr_id ?>"
                                    class="popover-class fa fa-info-circle text-info"
                                    data-toggle="popover" data-html="true" data-trigger="hover" data-placement="top"
                                    data-container="body" title="<?=Html::encode($product->pr_name)?>"
                                    data-content='<?=Html::encode($product->pr_description)?>'
                                ></a>
                            <?php endif; ?>
                        </h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="display: block">
                        <?php foreach ($orderRelatedQuotes as $productQuote) : ?>
                            <?php if ($productQuote->isFlight() && $productQuote->pq_product_id == $product->pr_id) :?>
                                <?php $flightProductQuotes[] = $productQuote; ?>
                            <?php endif; ?>

                            <?php if ($productQuote->isHotel() && $productQuote->pq_product_id == $product->pr_id) :?>
                                <?php $hotelProductQuotes[] = $productQuote; ?>
                            <?php endif; ?>

                        <?php endforeach; ?>

                        <?php if ($product->isFlight()) :?>
                            <?= $this->render('product_flight_quotes', [
                                    'data' => $flightProductQuotes,
                                    'productId' => $product->pr_id
                            ]) ?>
                        <?php endif; ?>

                        <?php if ($product->isHotel()) :?>
                            <? //= $this->render('product_flight_quotes', ['data' => $hotelProductQuotes]) ?>
                        <?php endif; ?>
                    </div>
                </div>
                    <?php $flightProductQuotes = []; ?>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<?php
$css = <<<CSS
    .x_panel_product {
        background-color: #CCE5FF;
    }
CSS;
$this->registerCss($css);
?>