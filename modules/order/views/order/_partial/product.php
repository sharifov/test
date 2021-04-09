<?php

use modules\order\src\entities\order\Order;

/**
 * @var Order $order
 */
?>

<div class="order-view-product-box">
    <div id="pjax-order-invoice-18" data-pjax-container="" data-pjax-timeout="10000">
        <div class="x_panel x_panel_product">
            <div class="x_title">
                <h2><i class="fas fa-file-invoice-dollar"></i> Product List</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">
                <?php \yii\helpers\VarDumper::dump($order->productQuotes); ?>
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