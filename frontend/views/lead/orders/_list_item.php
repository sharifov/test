<?php
/* @var $this yii\web\View */
/* @var $order Order */
/* @var $index integer */

use common\models\Order;
use yii\bootstrap4\Html;

?>

<div class="x_panel">
    <div class="x_title">

        <small><span class="badge badge-white">OR<?=($order->or_id)?></span></small>
        "<b><?=\yii\helpers\Html::encode($order->or_name)?></b>"
        (<span title="UID"><?=\yii\helpers\Html::encode($order->or_uid)?></span>)
        <?=$order->getStatusLabel()?>
        <?=$order->getPayStatusLabel()?>

        <ul class="nav navbar-right panel_toolbox">
            <!--            <li>-->
            <!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
            <!--            </li>-->
            <li>
                <?= Html::a('<i class="fa fa-edit warning"></i> Update order', null, [
                    'data-url' => \yii\helpers\Url::to(['/order/update-ajax', 'id' => $order->or_id]),
                    'class' => 'btn-update-order'
                ])?>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                <div class="dropdown-menu" role="menu">
                    <?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete order', null, [
                        'class' => 'dropdown-item text-danger btn-delete-order',
                        'data-order-id' => $order->or_id,
                        'data-url' => \yii\helpers\Url::to(['/order/delete-ajax']),
                    ]) ?>
                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

        <table class="table table-bordered">
            <?php if ($order->orderProducts):

                $ordTotalPrice = 0;
                $ordClientTotalPrice = 0;
                $ordOptionTotalPrice = 0;
                $ordTotalFee = 0;
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
                <?php if ($order->orderProducts):?>
                <?php foreach ($order->orderProducts as $product):
                    $quote = $product->orpProductQuote;
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

                        <!--                    <td>--><?//=\yii\helpers\VarDumper::dumpAsString($quote->attributes, 10, true)?><!--</td>-->

                        <td><?=Html::encode($quote->pq_name)?></td>
                        <td><?=$quote->getStatusLabel()?></td>
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
                                'data-url' => \yii\helpers\Url::to(['order-product/delete-ajax'])
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


                    $calcTotalPrice = round($ordTotalPrice + $ordOptionTotalPrice + $ordTotalFee, 2);
                    $calcClientTotalPrice = round($calcTotalPrice * $order->or_client_currency_rate, 2);

                ?>
                <tr>
                    <th class="text-right" colspan="5">Sub Total: </th>
                    <th class="text-right"><?=number_format($ordOptionTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($ordTotalFee, 2)?></th>
                    <th class="text-right"><?=number_format($ordTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($ordClientTotalPrice, 2)?> <?=Html::encode($order->or_client_currency)?></th>
                    <th></th>
                </tr>
                <tr>
                    <th class="text-right" colspan="5">Calc Total: </th>
                    <td class="text-center" colspan="2">(price + opt + fee)</td>
                    <th class="text-right"><?=number_format($calcTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($calcClientTotalPrice, 2)?> <?=Html::encode($order->or_client_currency)?></th>
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
                        <th></th>
                    </tr>
                    <?php if ($order->invoices):?>
                    <?php foreach ($order->invoices as $invoice):

                        $invTotalPrice += $invoice->inv_sum;
                        $invClientTotalPrice += $invoice->inv_client_sum;
                        ?>
                        <tr>
                            <td title="Invoice ID"><?=Html::encode($invoice->inv_id)?></td>
                            <td><?=$invoice->getStatusLabel()?></td>
                            <td>
                                <?=Html::encode($invoice->inv_description)?>
                            </td>
                            <td><?=$invoice->inv_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($invoice->inv_created_dt)) : '-'?></td>
                            <td class="text-right <?=$invoice->inv_sum > 0 ? 'text-success' : 'text-danger' ?>"><?=number_format($invoice->inv_sum, 2)?></td>
                            <td class="text-right" title="Currency Rate: <?=$invoice->inv_currency_rate?>"><?=number_format($invoice->inv_client_sum, 2)?> <?=Html::encode($invoice->inv_client_currency)?></td>
                            <td>

                                <?php
                                echo Html::a('<i class="fa fa-edit text-warning" title="Update"></i>', null, [
                                    'class' => 'btn-update-invoice',
                                    'data-url' => \yii\helpers\Url::to(['/invoice/update-ajax', 'id' => $invoice->inv_id])
                                ]);
                                ?>

                                <?php
                                echo Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i>', null, [
                                    'data-invoice-id' => $invoice->inv_id,
                                    'data-order-id' => $invoice->inv_order_id,
                                    'class' => 'btn-delete-invoice',
                                    'data-url' => \yii\helpers\Url::to(['/invoice/delete-ajax'])
                                ]);
                                ?>
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
                                    'data-url' => \yii\helpers\Url::to(['invoice/create-ajax', 'id' => $order->or_id, 'amount' => $newInvoiceAmount])
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