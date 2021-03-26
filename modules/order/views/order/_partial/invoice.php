<?php

use common\models\Currency;
use modules\invoice\src\entities\invoice\InvoiceStatus;
use modules\order\src\entities\order\Order;
use sales\auth\Auth;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var Order $order */

?>
<div class="order-view-invoice-box">
    <?php Pjax::begin(['id' => 'pjax-order-invoice-' . $order->or_id, 'enablePushState' => false, 'timeout' => 10000])?>

        <div class="x_panel x_panel_invoice">
            <div class="x_title">
                <h2><i class="fas fa-file-invoice-dollar"></i> Invoice List</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">
                <div class="x_panel">
                    <div class="x_title"></div>
                    <div class="x_content" style="display: block">
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
                                                    <?php if (Auth::can('/invoice/invoice/update-ajax')) : ?>
                                                        <?php
                                                        echo Html::a('<i class="fa fa-edit text-warning" title="Update"></i> Update', null, [
                                                            'class' => 'dropdown-item btn-update-invoice',
                                                            'data-url' => Url::to(['/invoice/invoice/update-ajax', 'id' => $invoice->inv_id])
                                                        ]);
                                                        ?>
                                                    <?php endif ?>
                                                    <?php if (Auth::can('/invoice/invoice-status-log/show')) : ?>
                                                        <?php
                                                        echo Html::a('<i class="fa fa-list" title="Status log"></i> Status Log', null, [
                                                            'class' => 'dropdown-item btn-invoice-status-log',
                                                            'data-url' => Url::to(['/invoice/invoice-status-log/show', 'gid' => $invoice->inv_gid]),
                                                            'data-gid' => $invoice->inv_gid,
                                                        ]);
                                                        ?>
                                                    <?php endif ?>
                                                    <div class="dropdown-divider"></div>
                                                    <?php if (Auth::can('/invoice/invoice/delete-ajax')) : ?>
                                                        <?php
                                                        echo Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i> Delete', null, [
                                                            'data-invoice-id' => $invoice->inv_id,
                                                            'data-order-id' => $invoice->inv_order_id,
                                                            'class' => 'dropdown-item btn-delete-invoice',
                                                            'data-url' => Url::to(['/invoice/invoice/delete-ajax'])
                                                        ]);
                                                        ?>
                                                    <?php endif ?>
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

                        <?php $calcTotalPrice = $order->getOrderTotalCalcSum() + $order->getOrderTipsAmount() ?>

                        <?php if ($invTotalPrice !== $calcTotalPrice) :
                            $newInvoiceAmount = round($calcTotalPrice - $invTotalPrice, 2);
                            ?>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th class="text-warning"><i class="fa fa-warning"></i> New Invoice</th>
                                        <th class="text-right">
                                            <?=number_format($calcTotalPrice, 2)?> - <?=number_format($invTotalPrice, 2)?> =
                                            <span class="<?=$newInvoiceAmount > 0 ? 'text-success' : 'text-danger' ?>">
                                                <?=number_format($newInvoiceAmount, 2)?> USD
                                            </span>
                                        </th>
                                        <th style="width: 120px">
                                            <?php if (Auth::can('/invoice/invoice/create-ajax')) : ?>
                                                <?php
                                                    echo Html::a('<i class="fa fa-plus-circle" title="Add new invoice"></i> create', null, [
                                                        'data-order-id' => $order->or_id,
                                                        'class' => 'btn btn-success btn-create-invoice',
                                                        'data-url' => Url::to(['/invoice/invoice/create-ajax', 'id' => $order->or_id, 'amount' => $newInvoiceAmount])
                                                    ]);
                                                ?>
                                            <?php endif ?>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php Pjax::end() ?>
</div>

<?php
$js = <<<JS
    $('body').off('click', '.btn-create-invoice').on('click', '.btn-create-invoice', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Add Invoice');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status === 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });
    
    $('body').off('click', '.btn-update-invoice').on('click', '.btn-update-invoice', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
                
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Invoice');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status === 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });
    
    $('body').off('click', '.btn-delete-invoice').on('click', '.btn-delete-invoice', function (e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to delete this Invoice?')) {
            return false;
        }
        
        let menu = $(this);
        let invoiceId = menu.data('invoice-id');
        let orderId = menu.data('order-id');
        let url = menu.data('url');
        $('#preloader').removeClass('d-none');
        
        $.ajax({
            url: url,
            type: 'post',
            data: {'id': invoiceId},
            dataType: 'json',
        })
        .done(function(data) {
            if (data.error) {
                createNotify('Error', 'Error: delete Invoice', 'error');
            } else {
                pjaxReload({container: '#pjax-order-invoice-' + orderId, timout: 8000});
                createNotify('Success', data.message, 'success');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.log({jqXHR : jqXHR, textStatus : textStatus, errorThrown : errorThrown}); 
            createNotify('Error', 'Server error. Try again later.', 'error');
        })
        .always(function() {
            $('#preloader').addClass('d-none');
        });
    });
    
    $('body').off('click', '.btn-invoice-status-log').on('click', '.btn-invoice-status-log', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let gid = $(this).data('gid');
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Invoice [' + gid + '] status history');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status === 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });
JS;
$this->registerJs($js, yii\web\View::POS_END);

$css = <<<CSS
    .x_panel_invoice {
        background-color: #e8e8e8;
    }
CSS;
$this->registerCss($css);
