    <?php
/* @var $this yii\web\View */
/* @var $order \modules\order\src\entities\order\Order */
/* @var $index integer */

	use common\models\Currency;
	use modules\invoice\src\entities\invoice\InvoiceStatus;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderStatus;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\bootstrap4\Html;

?>

<div class="x_panel">
    <div class="x_title">

        <small><span class="badge badge-white">OR<?=($order->or_id)?></span></small>
        "<b><?=\yii\helpers\Html::encode($order->or_name)?></b>"
        (<span title="UID"><?=\yii\helpers\Html::encode($order->or_uid)?></span>)
        <?= OrderStatus::asFormat($order->or_status_id) ?>
        <?= OrderPayStatus::asFormat($order->or_pay_status_id) ?>

        <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $order->or_profit_amount ?>

        <ul class="nav navbar-right panel_toolbox">
            <!--            <li>-->
            <!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
            <!--            </li>-->
            <li>
                <?= Html::a('<i class="fas fa-dollar-sign text-success"></i> Split Profit', null, [
                    'class' => 'text-success btn-split',
                    'data-url' => \yii\helpers\Url::to(['/order/order-user-profit/ajax-manage-order-user-profit']),
                    'data-order-id' => $order->or_id,
					'data-title' => 'Order User Profit',
				]) ?>
            </li>

			<?php if($order->orderTips): ?>
                <li>
                    <?= Html::a('<i class="fas fa-dollar-sign text-success"></i> Split Tips', null, [
                        'class' => 'text-success btn-split',
                        'data-url' => \yii\helpers\Url::to(['/order/order-tips-user-profit/ajax-manage-order-tips-user-profit']),
                        'data-order-id' => $order->or_id,
                        'data-title' => 'Order User Tips',
                    ]) ?>
                </li>
            <?php endif; ?>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle text-warning" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars"></i> Actions</a>
                <div class="dropdown-menu" role="menu">
                    <?php /*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

                    <?= Html::a('<i class="fa fa-edit"></i> Update order', null, [
                        'data-url' => \yii\helpers\Url::to(['/order/order/update-ajax', 'id' => $order->or_id]),
                        'class' => 'dropdown-item text-warning btn-update-order'
                    ])?>

                    <?= Html::a('<i class="fa fa-list"></i> Status log', null, [
                        'class' => 'dropdown-item btn-order-status-log',
                        'data-url' => \yii\helpers\Url::to(['/order/order-status-log/show', 'gid' => $order->or_gid]),
                        'data-gid' => $order->or_gid,
                    ]) ?>



                    <div class="dropdown-divider"></div>
                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete order', null, [
                        'class' => 'dropdown-item text-danger btn-delete-order',
                        'data-order-id' => $order->or_id,
                        'data-url' => \yii\helpers\Url::to(['/order/order/delete-ajax']),
                    ]) ?>
                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">
        <?php
            $ordTotalPrice = 0;
            $ordClientTotalPrice = 0;
            $ordOptionTotalPrice = 0;
            $ordTotalFee = 0;
        ?>

        <table class="table table-bordered">
            <?php if ($order->productQuotes):


                ?>
                <tr>
                    <th>Quote ID</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th title="Options, USD">Options, USD</th>
                    <th title="Service FEE">FEE</th>
                    <th title="Origin Price, USD">Price, USD</th>
                    <th>Client Price</th>
                    <th></th>
                </tr>
                <?php foreach ($order->productQuotes as $productQuote):
                    $quote = $productQuote;
                    $ordTotalPrice += $quote->pq_price;
                    $ordTotalFee += $quote->pq_service_fee_sum;
                    $ordClientTotalPrice += $quote->pq_client_price;
                    $ordOptionTotalPrice += $quote->optionAmountSum;
                    ?>
                    <tr>
                        <td title="Product Quote ID"><?=Html::encode($quote->pq_id)?></td>
                        <td title="<?=Html::encode($quote->pq_product_id)?>">
                            <?=Html::encode($quote->pqProduct->prType->pt_name)?>
                            <?=$quote->pqProduct->pr_name ? ' - ' . Html::encode($quote->pqProduct->pr_name) : ''?>
                        </td>

                        <!--                    <td>--><?php //=\yii\helpers\VarDumper::dumpAsString($quote->attributes, 10, true)?><!--</td>-->

                        <td><?=Html::encode($quote->pq_name)?></td>
                        <td><?=ProductQuoteStatus::asFormat($quote->pq_status_id)?></td>
                        <td><?=$quote->pq_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($quote->pq_created_dt)) : '-'?></td>
                        <td class="text-right"><?=number_format($quote->optionAmountSum, 2)?></td>
                        <td class="text-right"><?=number_format($quote->pq_service_fee_sum, 2)?></td>
                        <td class="text-right"><?=number_format($quote->pq_price, 2)?></td>
                        <td class="text-right"><?=number_format($quote->pq_client_price, 2)?> <?=Html::encode($quote->pq_client_currency)?></td>
                        <td>
                            <?php
                            echo Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i>', null, [
                                'data-order-id' => $order->or_id,
                                'data-product-quote-id' => $quote->pq_id,
                                'class' => 'btn-delete-quote-from-order',
                                'data-url' => \yii\helpers\Url::to(['/order/order-product/delete-ajax'])
                            ]);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php
                    $ordTotalPrice = round($ordTotalPrice, 2);
                    $ordClientTotalPrice = round($ordClientTotalPrice, 2);
                    $ordOptionTotalPrice = round($ordOptionTotalPrice, 2);
                    $ordTotalFee = round($ordTotalFee, 2);


                    $calcTotalPrice = round($ordTotalPrice + $ordOptionTotalPrice, 2);
                    $calcClientTotalPrice = round($calcTotalPrice * $order->or_client_currency_rate, 2);

                ?>
                <tr>
                    <th class="text-right" colspan="5">Order Amount: </th>
                    <th class="text-right"><?=number_format($ordOptionTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($ordTotalFee, 2)?></th>
                    <th class="text-right"><?=number_format($ordTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($ordClientTotalPrice, 2)?> <?=Html::encode($order->or_client_currency)?></th>
                    <th></th>
                </tr>
                <tr>
                    <th class="text-right" colspan="5">Calc Total: </th>
                    <td class="text-center" colspan="2">(price + opt)</td>
                    <th class="text-right"><?=number_format($calcTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($ordClientTotalPrice, 2)?> <?=Html::encode($order->or_client_currency)?></th>
                    <th></th>
                </tr>
                <tr>
                    <th class="text-right" colspan="5">Total: </th>
                    <td class="text-center" colspan="2">(DB)</td>
                    <th class="text-right"><?=number_format($order->or_app_total, 2)?></th>
                    <th class="text-right"><?=number_format($order->or_client_total, 2)?> <?=Html::encode($order->or_client_currency)?></th>
                    <th></th>
                </tr>
            <?php endif; ?>
        </table>

        <i class="fa fa-user"></i> <?=$order->orCreatedUser ? Html::encode($order->orCreatedUser->username) : '-'?>,
        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($order->or_created_dt)) ?>,
        <i class="fa fa-money" title="currency"></i> <?=Html::encode($order->or_client_currency)?> <span title="Rate: <?=$order->or_client_currency_rate?>">(<?=round($order->or_client_currency_rate, 3)?>)</span>

        <div class="text-right"><h4>Calc Total: <?=number_format($order->orderTotalCalcSum, 2)?> USD, Total: <?=number_format($order->or_app_total, 2)?> USD</h4></div>

        <hr>
        <?php \yii\widgets\Pjax::begin(['id' => 'pjax-order-invoice-' . $order->or_id, 'enablePushState' => false, 'timeout' => 10000])?>
            <h4><i class="fas fa-file-invoice-dollar"></i> Invoice List</h4>
            <?php
                $invTotalPrice = 0;
                $invClientTotalPrice = 0;
            ?>
            <table class="table table-bordered">
                <?php if ($order->invoices): ?>
                    <tr>
                        <th style="width: 100px">Invoice ID</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th title="Amount, USD">Amount, USD</th>
                        <th>Client Amount</th>
                        <th style="width: 60px"></th>
                    </tr>
                    <?php if ($order->invoices):?>
                    <?php foreach ($order->invoices as $invoice):

                        $invTotalPrice += $invoice->inv_sum;
                        $invClientTotalPrice += $invoice->inv_client_sum;
                        ?>
                        <tr>
                            <td title="Invoice ID"><?=Html::encode($invoice->inv_id)?></td>
                            <td><?= InvoiceStatus::asFormat($invoice->inv_status_id) ?></td>
                            <td>
                                <?=Html::encode($invoice->inv_description)?>
                            </td>
                            <td><?=$invoice->inv_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($invoice->inv_created_dt)) : '-'?></td>
                            <td class="text-right <?=$invoice->inv_sum > 0 ? 'text-success' : 'text-danger' ?>"><?=number_format($invoice->inv_sum, 2)?></td>
                            <td class="text-right" title="Currency Rate: <?=$invoice->inv_currency_rate?>"><?=number_format($invoice->inv_client_sum, 2)?> <?=Html::encode($invoice->inv_client_currency)?></td>
                            <td>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu">

                                        <?php
                                        echo Html::a('<i class="fa fa-edit text-warning" title="Update"></i> Update', null, [
                                            'class' => 'dropdown-item btn-update-invoice',
                                            'data-url' => \yii\helpers\Url::to(['/invoice/invoice/update-ajax', 'id' => $invoice->inv_id])
                                        ]);
                                        ?>

                                        <?php
                                        echo Html::a('<i class="fa fa-list" title="Status log"></i> Status Log', null, [
                                            'class' => 'dropdown-item btn-invoice-status-log',
                                            'data-url' => \yii\helpers\Url::to(['/invoice/invoice-status-log/show', 'gid' => $invoice->inv_gid]),
                                            'data-gid' => $invoice->inv_gid,
                                        ]);
                                        ?>
                                        <div class="dropdown-divider"></div>
                                        <?php
                                        echo Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i> Delete', null, [
                                            'data-invoice-id' => $invoice->inv_id,
                                            'data-order-id' => $invoice->inv_order_id,
                                            'class' => 'dropdown-item btn-delete-invoice',
                                            'data-url' => \yii\helpers\Url::to(['/invoice/invoice/delete-ajax'])
                                        ]);
                                        ?>


                                    </div>
                                </div>


                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php
                        $invTotalPrice = round($invTotalPrice, 2);
                        $invClientTotalPrice = round($invClientTotalPrice, 2);
                    ?>
                    <tr>
                        <th class="text-right" colspan="4">Total: </th>
                        <th class="text-right"><?=number_format($invTotalPrice, 2)?></th>
                        <th class="text-right"><?=number_format($invClientTotalPrice, 2)?> <?=Html::encode($invoice->inv_client_currency)?></th>
                        <th></th>
                    </tr>
                <?php endif; ?>
                <?php endif; ?>
            </table>

        <?php if($order->orderTips): ?>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Client Amount</th>
                        <th>Amount</th>
                        <th>User Profit</th>
                        <th>User Profit Percent</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $order->orderTips->ot_client_amount ?> (<?= $order->orClientCurrency ? $order->orClientCurrency->cur_code : Currency::getDefaultCurrencyCode() ?>)</td>
                        <td><?= $order->orderTips->ot_amount ?> (<?= Currency::getDefaultCurrencyCode() ?>)</td>
                        <td><?= $order->orderTips->ot_user_profit ?> (<?= Currency::getDefaultCurrencyCode() ?>)</td>
                        <td><?= $order->orderTips->ot_user_profit_percent ?>%</td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>


            <?php if ($invTotalPrice !== $ordTotalPrice):
                $newInvoiceAmount = round($ordTotalPrice - $invTotalPrice, 2);
            ?>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="text-warning"><i class="fa fa-warning"></i> New Invoice</th>
                            <th class="text-right">
                                <?=number_format($ordTotalPrice, 2)?> - <?=number_format($invTotalPrice, 2)?> =
                                <span class="<?=$newInvoiceAmount > 0 ? 'text-success' : 'text-danger' ?>">
                                    <?=number_format($newInvoiceAmount, 2)?> USD
                                </span>
                            </th>
                            <th style="width: 120px">
                                <?php
                                echo Html::a('<i class="fa fa-plus-circle" title="Add new invoice"></i> create', null, [
                                    'data-order-id' => $order->or_id,
                                    'class' => 'btn btn-success btn-create-invoice',
                                    'data-url' => \yii\helpers\Url::to(['/invoice/invoice/create-ajax', 'id' => $order->or_id, 'amount' => $newInvoiceAmount])
                                ]);
                                ?>
                            </th>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php \yii\widgets\Pjax::end() ?>
    </div>


</div>