<?php

/* @var $this yii\web\View */
/* @var $order \modules\order\src\entities\order\Order */
/* @var $index integer */
/* @var $case \src\entities\cases\Cases */
/* @var $caseAbacDto \modules\cases\src\abac\dto\CasesAbacDto */

use common\models\Currency;
use common\models\Payment;
use modules\invoice\src\entities\invoice\InvoiceStatus;
use modules\order\src\abac\dto\OrderAbacDto;
use modules\order\src\abac\OrderAbacObject;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\processManager\phoneToBook\OrderProcessManager;
use modules\order\src\processManager\Status;
use modules\order\src\transaction\services\TransactionService;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use src\auth\Auth;
use src\helpers\product\ProductQuoteHelper;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$process = OrderProcessManager::findOne($order->or_id);
$formatter = new \common\components\i18n\Formatter();

$orderAbacDto = new OrderAbacDto($order);
?>

<div class="x_panel">
    <div class="x_title">
        <small data-toggle="tooltip" data-original-title="Order UID: <?=\yii\helpers\Html::encode($order->or_uid)?>, Order GID: <?=\yii\helpers\Html::encode($order->or_gid)?>"
               title="Order UID: <?=\yii\helpers\Html::encode($order->or_uid)?>, Order GID: <?=\yii\helpers\Html::encode($order->or_gid)?>">
            <span class="badge badge-white">
                <?php echo 'OR ' . $order->or_id ?>
                <?php /* if (Auth::can('/order/order/view')) : ?>
                    <?php
                        echo Html::a('OR ' . $order->or_id . ' <span class="glyphicon glyphicon-eye-open"></span>', ['/order/order/view', 'gid' => $order->or_gid], [
                            'target' => '_blank',
                            'data-pjax' => 0,
                        ])
                    ?>
                <?php else : ?>
                    <?php echo 'OR ' . $order->or_id ?>
                <?php endif*/ ?>
            </span>
        </small>
        <?php /*(<span title="GID: <?=\yii\helpers\Html::encode($order->or_gid)?>"><?=\yii\helpers\Html::encode($order->or_uid)?></span>)*/ ?>
        <?= $order->or_project_id ? $formatter->asProjectName($order->or_project_id) : null ?>
        <?php if ($order->or_name) : ?>
        "<b><?=\yii\helpers\Html::encode($order->or_name)?></b>"
        <?php endif; ?>
        -
        <?= OrderStatus::asFormat($order->or_status_id) ?>
        <?= OrderPayStatus::asFormat($order->or_pay_status_id) ?>



        <?php /* if ($order->or_profit_amount > 0) : ?>
            <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $order->or_profit_amount ?>
        <?php endif; */ ?>
        <?php if ($process) : ?>
            &nbsp;&nbsp;&nbsp;&nbsp;Auto Process: (<?= Status::LIST[$process->opm_status] ?? 'undefined'?>)
        <?php endif; ?>
        <ul class="nav navbar-right panel_toolbox">
            <!--            <li>-->
            <!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
            <!--            </li>-->
          <?php /*
            <li>
                <?= Html::a('<i class="fas fa-dollar-sign text-success"></i> Split Profit', null, [
                    'class' => 'text-success btn-split',
                    'data-url' => \yii\helpers\Url::to(['/order/order-user-profit/ajax-manage-order-user-profit']),
                    'data-order-id' => $order->or_id,
                    'data-title' => 'Order User Profit',
                ]) ?>
            </li>

            <?php if ($order->orderTips) : ?>
                <li>
                    <?= Html::a('<i class="fas fa-dollar-sign text-success"></i> Split Tips', null, [
                        'class' => 'text-success btn-split',
                        'data-url' => \yii\helpers\Url::to(['/order/order-tips-user-profit/ajax-manage-order-tips-user-profit']),
                        'data-order-id' => $order->or_id,
                        'data-title' => 'Order User Tips',
                    ]) ?>
                </li>
            <?php endif; ?>
 */ ?>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle text-warning" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars"></i> Actions</a>
                <div class="dropdown-menu" role="menu">
                    <?php /*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>
                    <?php if (Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_DETAIL_VIEW, OrderAbacObject::ACTION_ACCESS)) : ?>
                        <?php
                        echo Html::a('<i class="glyphicon glyphicon-eye-open"></i> View Details', ['/order/order/view', 'gid' => $order->or_gid], [
                            'target' => '_blank',
                            'data-pjax' => 0,
                            'class' => 'dropdown-item'
                        ])
                        ?>
                    <?php endif; ?>

                    <?php if ($process) : ?>
                        <?php if ($process->isRunning()) : ?>
                            <?php if (Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_CANCEL_AUTO_PROCESSING, OrderAbacObject::ACTION_ACCESS)) : ?>
                                <?= Html::a('Cancel Auto Process', null, [
                                    'data-url' => \yii\helpers\Url::to(['/order/order-process-actions/cancel-process']),
                                    'class' => 'dropdown-item btn-cancel-process',
                                    'data-order-id' => $order->or_id,
                                ])?>
                            <?php endif;?>
                        <?php endif;?>
                    <?php else : ?>
                        <?php if (Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_START_AUTO_PROCESSING, OrderAbacObject::ACTION_ACCESS)) : ?>
                            <?= Html::a('<i class="fa fa-play-circle-o"></i> Start Auto Processing', null, [
                                'data-url' => \yii\helpers\Url::to(['/order/order-process-actions/start-process']),
                                'class' => 'dropdown-item btn-start-process',
                                'data-order-id' => $order->or_id,
                            ])?>
                        <?php endif;?>
                    <?php endif;?>



                    <?php if (Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_COMPLETE, OrderAbacObject::ACTION_ACCESS)) : ?>
                        <?= Html::a('Complete Order', null, [
                            'data-url' => \yii\helpers\Url::to(['/order/order-actions/complete', 'orderId' => $order->or_id]),
                            'class' => 'dropdown-item btn-complete-order'
                        ])?>
                    <?php endif ?>

                    <?php if (Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_SEND_EMAIL_CONFIRMATION, OrderAbacObject::ACTION_ACCESS)) : ?>
                        <?= Html::a('<i class="fa fa-envelope-o"></i> Send Email Confirmation', null, [
                            'data-url' => \yii\helpers\Url::to(['/order/order-actions/send-email-confirmation']),
                            'data-id' => $order->or_id,
                            'class' => 'dropdown-item btn-order-send-email-confirmation'
                        ])?>
                    <?php endif ?>

                    <?php if (Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_GENERATE_PDF, OrderAbacObject::ACTION_ACCESS)) : ?>
                        <?= Html::a('<i class="fa fa-file-pdf-o"></i> Generate PDF', null, [
                            'data-url' => \yii\helpers\Url::to(['/order/order-actions/generate-files']),
                            'data-id' => $order->or_id,
                            'class' => 'dropdown-item btn-order-generate-files'
                        ])?>
                    <?php endif ?>

                    <?php if (Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_UPDATE, OrderAbacObject::ACTION_ACCESS)) : ?>
                        <?= Html::a('<i class="fa fa-edit"></i> Update order', null, [
                            'data-url' => \yii\helpers\Url::to(['/order/order/update-ajax', 'id' => $order->or_id]),
                            'class' => 'dropdown-item text-warning btn-update-order'
                        ])?>
                    <?php endif ?>

                    <?php if (Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_STATUS_LOG, OrderAbacObject::ACTION_ACCESS)) : ?>
                        <?= Html::a('<i class="fa fa-list"></i> Status log', null, [
                            'class' => 'dropdown-item btn-order-status-log',
                            'data-url' => \yii\helpers\Url::to(['/order/order-status-log/show', 'gid' => $order->or_gid]),
                            'data-gid' => $order->or_gid,
                        ]) ?>
                    <?php endif; ?>

                    <div class="dropdown-divider"></div>
                    <?php if (Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_CANCEL_ORDER, OrderAbacObject::ACTION_ACCESS)) : ?>
                        <?= Html::a('<i class="fa fa-remove"></i> Cancel Order', null, [
                            'data-url' => \yii\helpers\Url::to(['/order/order-actions/cancel', 'orderId' => $order->or_id]),
                            'class' => 'dropdown-item btn-cancel-order text-danger'
                        ])?>
                    <?php endif ?>

                    <?php if (Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_DELETE, OrderAbacObject::ACTION_ACCESS)) : ?>
                        <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete order', null, [
                            'class' => 'dropdown-item text-danger btn-delete-order',
                            'data-order-id' => $order->or_id,
                            'data-url' => \yii\helpers\Url::to(['/order/order/delete-ajax']),
                        ]) ?>
                    <?php endif; ?>
                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block; overflow-x: auto;">
        <?php
            $ordTotalPrice = 0;
            $ordClientTotalPrice = 0;
            $ordOptionTotalPrice = 0;
            $ordTotalFee = 0;
            $calcTotalPrice = 0;
            $orderTipsAmount = 0.00;
            $orderTipsAmountClient = 0.00;
        ?>

        <table class="table table-bordered">
            <?php if ($order->productQuotes) :
                $nr = 1;
                ?>
                <tr>
                    <th>Nr</th>
                    <th>Product</th>
                    <th>Booking ID</th>
                    <th>Status</th>
                    <th title="Product Quote Options" style="width: 50px">Opt</th>
                    <th>Created</th>
                    <th>Client Price</th>
                    <th></th>
                </tr>
                <?php
                foreach ($order->nonReprotectionProductQuotes as $productQuote) :
//                    $ordTotalPrice += $productQuote->pq_price;
//                    $ordTotalFee += $productQuote->pq_service_fee_sum;
//                    $ordClientTotalPrice += $productQuote->pq_client_price;
//                    $ordOptionTotalPrice += $productQuote->optionAmountSum;
                    ?>
                      <tr>
                          <?= $this->render('_reprotection_quote_item', [
                              'quote' => $productQuote,
                              'nr' => $nr++,
                              'order' => $order,
                              'isReprotection' => false,
                              'case' => $case,
                              'caseAbacDto' => $caseAbacDto,
                              'projectId' => $order->or_project_id
                          ]) ?>
                      </tr>
                <?php endforeach; ?>
                <?php
//                    $ordTotalPrice = round($ordTotalPrice, 2);
//                    $ordClientTotalPrice = round($ordClientTotalPrice, 2);
//                    $ordOptionTotalPrice = round($ordOptionTotalPrice, 2);
//                    $ordTotalFee = round($ordTotalFee, 2);

//                    $orderTipsAmount = $order->orderTips->ot_amount ?? 0.00;
                    $orderTipsAmountClient = $order->orderTips->ot_client_amount ?? 0.00;

//                    $calcTotalPrice = round($ordTotalPrice + $ordOptionTotalPrice + $orderTipsAmount, 2);
//                    $calcClientTotalPrice = round(($calcTotalPrice) * $order->or_client_currency_rate, 2);

                ?>
                <?php /*
                <tr>
                    <th class="text-right" colspan="2">Order Amount: </th>
                    <th class="text-right"><?=number_format($ordOptionTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($ordTotalFee, 2)?></th>
                    <th class="text-right"><?=number_format($ordTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($ordClientTotalPrice, 2)?> <?=Html::encode($order->or_client_currency)?></th>
                    <th></th>
                </tr>
                <tr>
                    <th class="text-right" colspan="2">Tips: </th>
                    <td class="text-center" colspan="2">(DB)</td>
                    <th class="text-right"><?=number_format($orderTipsAmount, 2)?></th>
                    <th class="text-right"><?=number_format($orderTipsAmountClient, 2)?> <?=Html::encode($order->or_client_currency)?></th>
                    <th></th>
                </tr>
 */ ?>
                <tr title="From DB">
                    <th class="text-right" colspan="6">Total (price + opt + tips): </th>
                    <th class="text-right"><?=number_format($order->or_client_total + $orderTipsAmountClient, 2)?> <?=Html::encode($order->or_client_currency)?></th>
                    <td></td>
                </tr>
            <?php endif; ?>
        </table>

        <?php if ($order->or_created_user_id && $order->orCreatedUser) : ?>
        <i class="fa fa-user"></i> <?=$order->orCreatedUser ? Html::encode($order->orCreatedUser->username) : '-'?>,
        <?php endif; ?>
        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($order->or_created_dt)) ?>,
        <i class="fa fa-money" title="currency"></i> <?=Html::encode($order->or_client_currency)?> <span title="Rate: <?=$order->or_client_currency_rate?>">(<?=round($order->or_client_currency_rate, 3)?>)</span>

      <?php /*
        <div class="text-right"><h4>Calc Total: <?=number_format($order->orderTotalCalcSum  + $orderTipsAmount, 2)?> USD, Total: <?=number_format($order->or_client_total + $orderTipsAmountClient, 2)?> <?=Html::encode($order->or_client_currency)?></h4></div>
 */ ?>
        <?php /*
        <?php \yii\widgets\Pjax::begin(['id' => 'pjax-order-invoice-' . $order->or_id, 'enablePushState' => false, 'timeout' => 10000])?>
            <h4><i class="fas fa-file-invoice-dollar"></i> Invoice List</h4>
            <?php
                $invTotalPrice = 0;
                $invClientTotalPrice = 0;
            ?>
            <table class="table table-bordered">
                <?php if ($order->invoices) : ?>
                    <tr>
                        <th style="width: 100px">Invoice ID</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th title="Amount, USD">Amount, USD</th>
                        <th>Client Amount</th>
                        <th style="width: 60px"></th>
                    </tr>
                    <?php if ($order->invoices) :?>
                        <?php foreach ($order->invoices as $invoice) :
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

        <?php if ($order->orderTips) : ?>
            <h4><i class="fas fa-file-invoice-dollar"></i> Order Tips</h4>
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


            <?php if ($invTotalPrice !== $calcTotalPrice) :
                $newInvoiceAmount = round($calcTotalPrice - $invTotalPrice, 2);
                ?>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="text-warning"><i class="fa fa-warning"></i> New Invoice</th>
                            <th class="text-right">
                                <?=number_format($calcTotalPrice + $orderTipsAmount, 2)?> - <?=number_format($invTotalPrice, 2)?> =
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

        <hr>
        <?php \yii\widgets\Pjax::begin(['id' => 'pjax-order-payment-' . $order->or_id, 'enablePushState' => false, 'timeout' => 10000])?>
        <h4><i class="fas fa-file-invoice-dollar"></i> Payment List</h4>
        <?php
        $paymentTotalPrice = 0;
        $paymentClientTotalPrice = 0;
        $payments = Payment::find()->andWhere(['pay_order_id' => $order->or_id])->all();
        ?>
            <?php if ($payments) : ?>
             <table class="table table-bordered">
                <tr>
                    <th style="width: 100px">Payment ID</th>
                    <th>Created</th>
                    <th>Status</th>
                    <th>Method</th>
                    <th title="Amount, USD">Amount, USD</th>
                    <th style="width: 60px"></th>
                </tr>

                    <?php foreach ($payments as $payment) :
                        $paymentTotalPrice += $payment->pay_amount;
                        ?>
                        <tr class="payment_row_<?php echo $payment->pay_id ?>">
                            <td title="Payment ID"><?=Html::encode($payment->pay_id)?></td>
                            <td><?=$payment->pay_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($payment->pay_created_dt)) : '-'?></td>
                            <td><?= Payment::getStatusName($payment->pay_status_id) ?></td>
                            <td>
                                <?php if ($payment->pay_method_id) {
                            echo $payment->payMethod->pm_name;
                        } ?>
                            </td>
                            <td class="text-right <?=$payment->pay_amount > 0 ? 'text-success' : 'text-danger' ?>"><?=number_format($payment->pay_amount, 2)?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu">

                                        <?php
                                        if (Auth::can('/order/payment-actions/capture')) {
                                            echo Html::a(
                                                '<i class="fa fa-credit-card text-success" title="Capture"></i> Capture',
                                                null,
                                                [
                                                        'class' => 'dropdown-item btn-payment-capture',
                                                        'data-url' => \yii\helpers\Url::to([
                                                            '/order/payment-actions/capture',
                                                        ]),
                                                        'data-payment-id' => $payment->pay_id,
                                                ]
                                            );
                                        }
                                        ?>

                                        <?php
                                        if (Auth::can('/order/payment-actions/refund')) {
                                            echo Html::a(
                                                '<i class="fa fa-credit-card text-danger" title="Refund"></i> Refund',
                                                null,
                                                [
                                                    'class' => 'dropdown-item btn-payment-refund',
                                                    'data-url' => \yii\helpers\Url::to([
                                                        '/order/payment-actions/refund', 'id' => $payment->pay_id
                                                    ]),
                                                ]
                                            );
                                        }
                                        ?>

                                        <?php
                                        if (Auth::can('/order/payment-actions/update')) {
                                            echo Html::a(
                                                '<i class="fa fa-edit text-warning" title="Update"></i> Update',
                                                null,
                                                [
                                                    'class' => 'dropdown-item btn-payment-update',
                                                    'data-url' => \yii\helpers\Url::to([
                                                        '/order/payment-actions/update',
                                                        'id' => $payment->pay_id
                                                    ])
                                                ]
                                            );
                                        }
                                        ?>

                                        <?php
                                        if (Auth::can('/order/payment-actions/status-log')) {
                                            echo Html::a(
                                                '<i class="fa fa-list" title="Status Log"></i> Status Log',
                                                null,
                                                [
                                                    'class' => 'dropdown-item btn-payment-status-log',
                                                    'data-url' => \yii\helpers\Url::to([
                                                        '/order/payment-actions/status-log',
                                                        'id' => $payment->pay_id
                                                    ])
                                                ]
                                            );
                                        }
                                        ?>

                                        <?php
                                        if (Auth::can('/order/payment-actions/delete')) {
                                            echo Html::a(
                                                '<i class="glyphicon glyphicon-remove-circle text-danger" title="Delete"></i> Delete',
                                                null,
                                                [
                                                    'class' => 'dropdown-item btn-payment-delete',
                                                    'data-url' => \yii\helpers\Url::to([
                                                        '/order/payment-actions/delete',
                                                    ]),
                                                    'data-payment-id' => $payment->pay_id,
                                                    'data-order-id' => $order->or_id,
                                                ]
                                            );
                                        }
                                        ?>

                                    </div>
                                </div>


                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php
                    $paymentTotalPrice = round($paymentTotalPrice, 2);
                    ?>
                    <tr>
                        <th class="text-right" colspan="4">Total: </th>
                        <th class="text-right"><?=number_format($paymentTotalPrice, 2)?></th>
                        <th></th>
                    </tr>
                </table>
            <?php endif; ?>

        <?php \yii\widgets\Pjax::end() ?>

        <hr>
        <?php if (Auth::can('global/transaction/list/view')) : ?>
            <?php Pjax::begin(['id' => 'pjax-order-transaction-' . $order->or_id, 'enablePushState' => false, 'timeout' => 10000])?>
                <?php if ($transactions = TransactionService::getTransactionsByOrder($order->or_id)) : ?>
                    <h4><i class="fa fa-exchange"></i> Transaction List</h4>
                    <?php echo $this->render('@frontend/views/transaction/_partial/_transaction_table', [
                        'transactions' => $transactions,
                    ]) ?>
                <?php endif ?>
            <?php Pjax::end() ?>
        <?php endif ?>

      */ ?>

    </div>


</div>

<?php

$js = <<<JS
$('body').off('click', '.has_reprotection_quotes').on('click', '.has_reprotection_quotes', function (e) {
    e.preventDefault();
    
    let tr = $(this).closest('tr');
    
    tr.next(1).toggleClass('hidden');
});

$('body').off('click', '.btn_voluntary_offer_email').on('click', '.btn_voluntary_offer_email', function (e) {
    e.preventDefault();
    
    let btn = $(this);
    let btnIconHtml = btn.find('i')[0];
    let iconSpinner = '<i class="fa fa-spin fa-spinner"></i>';
    let url = btn.data('url');
    
    btn.find('i').replaceWith(iconSpinner);
    btn.addClass('disabled');
    
    let modal = $('#modal-md');
    $('#modal-md-label').html('Send Flight Voluntary Change Email');
    modal.find('.modal-body').html('');
    let id = $(this).attr('data-id');
    modal.find('.modal-body').load(url, function( response, status, xhr ) {
        if(status === 'error') {
            createNotify('Error', xhr.responseText, 'error');
        } else {
          modal.modal('show');
        }
        btn.find('i').replaceWith(btnIconHtml);
        btn.removeClass('disabled');
    });
});

$('body').off('click', '.btn-send-reprotection-quote-email').on('click', '.btn-send-reprotection-quote-email', function (e) {
    e.preventDefault();
    
    let btn = $(this);
    let btnIconHtml = btn.find('i')[0];
    let iconSpinner = '<i class="fa fa-spin fa-spinner"></i>';
    let url = btn.data('url');
    
    btn.find('i').replaceWith(iconSpinner);
    btn.addClass('disabled');
    
    let modal = $('#modal-md');
    $('#modal-md-label').html('Send Flight Schedule Change Email');
    modal.find('.modal-body').html('');
    let id = $(this).attr('data-id');
    modal.find('.modal-body').load(url, function( response, status, xhr ) {
        if(status === 'error') {
            createNotify('Error', xhr.responseText, 'error');
        } else {
          modal.modal('show');
        }
        btn.find('i').replaceWith(btnIconHtml);
        btn.removeClass('disabled');
    });
});
$('body').off('click', '.btn-send-voluntary-refund-quote-email').on('click', '.btn-send-voluntary-refund-quote-email', function (e) {
    e.preventDefault();
    
    let btn = $(this);
    let btnIconHtml = btn.find('i')[0];
    let iconSpinner = '<i class="fa fa-spin fa-spinner"></i>';
    let url = btn.data('url');
    
    btn.find('i').replaceWith(iconSpinner);
    btn.addClass('disabled');
    
    let modal = $('#modal-md');
    $('#modal-md-label').html('Send Flight Voluntary Refund Email');
    modal.find('.modal-body').html('');
    modal.find('.modal-body').load(url, function( response, status, xhr ) {
        if(status === 'error') {
            createNotify('Error', xhr.responseText, 'error');
        } else {
          modal.modal('show');
        }
        btn.find('i').replaceWith(btnIconHtml);
        btn.removeClass('disabled');
    });
});
$('body').off('click', '.btn-origin-reprotection-quote-diff').on('click', '.btn-origin-reprotection-quote-diff', function (e) {
    e.preventDefault();
    
    let btn = $(this);
    let btnIconHtml = btn.find('i')[0];
    let iconSpinner = '<i class="fa fa-spin fa-spinner"></i>';
    let url = btn.data('url');
    
    btn.find('i').replaceWith(iconSpinner);
    btn.addClass('disabled');
    
    let modal = $('#modal-lg');
    $('#modal-lg-label').html('Origin And Change Quotes Difference');
    modal.find('.modal-body').html('');
    modal.find('.modal-body').load(url, function( response, status, xhr ) {
        if(status === 'error') {
            createNotify('Error', xhr.responseText, 'error');
        } else {
          modal.modal('show');
        }
        btn.find('i').replaceWith(btnIconHtml);
        btn.removeClass('disabled');
    });
});
$('body').off('click', '.btn-reprotection-confirm').on('click', '.btn-reprotection-confirm', function (e) {
    e.preventDefault();
    
    let btn = $(this);
    let btnIconHtml = btn.find('i')[0];
    let iconSpinner = '<i class="fa fa-spin fa-spinner"></i>';
    let url = btn.data('url');
    
    btn.find('i').replaceWith(iconSpinner);
    btn.addClass('disabled');
    
    $.ajax({
        type: 'POST',
        data: {
            quoteId: btn.data('reprotection-quote-id'),
            case_id: btn.data('case_id'),
            order_id: btn.data('order_id'),
            pqc_id: btn.data('pqc_id')
        },
        url: url     
    })
    .done(function (data) {
        btn.find('i').replaceWith(btnIconHtml);
        btn.removeClass('disabled');
        if (data.error) {
            createNotify('Reprotection confirm', data.message, 'error');
        } else {
            createNotify('Reprotection confirm', 'Success', 'success');
        }
    })
    .fail(function () {
        btn.find('i').replaceWith(btnIconHtml);
        btn.removeClass('disabled');
        createNotify('Reprotection confirm', 'Server error', 'error');
    });
});
$('body').off('click', '.btn-reprotection-refund, .btn-reprotection-recommended, .btn-reprotection-decline').on('click', '.btn-reprotection-refund, .btn-reprotection-recommended, .btn-reprotection-decline', function (e) {
    e.preventDefault();
    if(!confirm('Are you sure?')) {
        return false;
    } 

    let btn = $(this);
    let btnIconHtml = btn.find('i')[0];
    let iconSpinner = '<i class="fa fa-spin fa-spinner"></i>';
    let url = btn.data('url');
    let title = btn.data('title');
    
    btn.find('i').replaceWith(iconSpinner);
    btn.addClass('disabled');
    
    $.ajax({
        type: 'POST',
        data: {
            quoteId: btn.data('reprotection-quote-id'),
            case_id: btn.data('case_id'),
            order_id: btn.data('order_id'),
            pqc_id: btn.data('pqc_id')
        },
        url: url     
    })
    .done(function (data) {
        btn.find('i').replaceWith(btnIconHtml);
        btn.removeClass('disabled');
        if (data.error) {
            createNotify(title, data.message, 'error');
        } else {
            $.pjax.reload({container: '#pjax-case-orders', push: false, replace: false, timeout: 10000, async: false});
            createNotify(title, 'Success', 'success');
        }
    })
    .fail(function (xhr) {
        btn.find('i').replaceWith(btnIconHtml);
        btn.removeClass('disabled');
        createNotify(title, xhr.responseText, 'error');
    });
});
JS;
$this->registerJs($js);

$css = <<<CSS
.has_reprotection_quotes, .has_reprotection_quotes:focus {
    text-decoration: underline;
}
CSS;
$this->registerCss($css);
