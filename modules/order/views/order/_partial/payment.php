<?php

use common\models\Payment;
use modules\order\src\entities\order\Order;
use sales\auth\Auth;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var Order $order */
?>
<?php
    $paymentTotalPrice = 0;
    $paymentClientTotalPrice = 0;
    $payments = $order->payments;
?>
<div class="order-view-payment-box">
    <?php Pjax::begin(['id' => 'pjax-order-payment-' . $order->or_id, 'enablePushState' => false, 'timeout' => 10000])?>

        <div class="x_panel x_panel_payment">
            <div class="x_title">
                <h2><i class="fas fa-credit-card"></i> Payment List <sup>(<?php echo count($payments) ?>)</sup></h2>
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
                    <div class="x_content" style="display: <?php echo $payments ? 'block' : 'none' ?>">

                        <?php if ($payments) : ?>
                        <table class="table table-bordered table-hover table-striped">
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
                                    <td title="Payment ID">
                                        <?php if (Auth::can('/payment/view')) : ?>
                                            <?php echo Html::a($payment->pay_id, ['/payment/view', 'id' => $payment->pay_id], ['target' => '_blank', 'data-pjax' => 0]) ?>
                                        <?php else : ?>
                                            <?php echo $payment->pay_id ?>
                                        <?php endif ?>
                                    </td>
                                    <td><?=$payment->pay_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($payment->pay_created_dt)) : '-'?></td>
                                    <td><?= Payment::getStatusName($payment->pay_status_id) ?></td>
                                    <td>
                                        <?php echo $payment->pay_method_id ? $payment->payMethod->pm_name : '' ?>
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
                                                            'data-url' => Url::to([
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
                                                            'data-url' => Url::to([
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
                                                            'data-url' => Url::to([
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
                                                            'data-url' => Url::to([
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
                                                            'data-url' => Url::to([
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
                    </div>
                </div>
            </div>
        </div>

    <?php Pjax::end() ?>
</div>

<?php
$js = <<<JS
    $('body').off('click', '.btn-payment-delete').on('click', '.btn-payment-delete', function (e) {
         e.preventDefault();
        if(!confirm('Are you sure you want to delete this Payment?')) {
            return false;
        }
    
        let url = $(this).data('url');
        let paymentId = $(this).data('payment-id');
        let orderId = $(this).data('order-id');
        $('#preloader').removeClass('d-none');
        
        $.ajax({
            url: url,
            type: 'post',
            data: {id: paymentId},
            dataType: 'json'
        })
        .done(function(data) {
            if (data.error) {
                createNotify('Error: delete Payment', data.error, 'error'); 
            } else {

                pjaxReload({container: '#pjax-order-payment-' + orderId, timout: 8000, async: true, replace: true});
                
                $('#pjax-order-payment-' + orderId).on('pjax:end', function (data, xhr) {
                    if ($('#pjax-order-transaction-' + orderId).length) {
                        pjaxReload({container: '#pjax-order-transaction-' + orderId, async: true, replace: true});
                    }
                });
                
                createNotify('Payment was successfully deleted', data.message, 'success');
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
    
    $('body').off('click', '.btn-payment-void').on('click', '.btn-payment-void', function (e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to Void this Payment?')) {
            return false;
        }
    
        let url = $(this).data('url');
        let paymentId = $(this).data('payment-id');
        $('#preloader').removeClass('d-none');
        
        $.ajax({
            url: url,
            type: 'post',
            data: {id: paymentId},
            dataType: 'json'
        })
        .done(function(data) {
            if (data.error) {
                createNotify('Error: Void Payment', data.message, 'error');
            } else {
                pjaxReload({container: '#pjax-order-payment-' + paymentId, timout: 8000});
                createNotify('Payment was successfully Void', 'Success', 'success');
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log({jqXHR : jqXHR, textStatus : textStatus, errorThrown : errorThrown}); 
            createNotify('Error', 'Server error. Try again later.', 'error');
        })
        .always(function() {
            $('#preloader').addClass('d-none');
        });
    });
    
    $('body').off('click', '.btn-payment-capture').on('click', '.btn-payment-capture', function (e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to Capture this Payment?')) {
            return false;
        }
    
        let url = $(this).data('url');
        let paymentId = $(this).data('payment-id');
    
        $.ajax({
            url: url,
            type: 'post',
            data: {id: paymentId},
            dataType: 'json'
        })
        .done(function(data) {
            if (data.error) {
                createNotify('Error: Capture Payment', data.message, 'error');
            } else {
                pjaxReload({container: '#pjax-order-payment-' + paymentId, timout: 8000});
                createNotify('Payment was successfully Capture', 'Success', 'success');
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
    
    $('body').off('click', '.btn-payment-refund').on('click', '.btn-payment-refund', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Payment Refund');
        modal.find('.modal-body').load(url, function(response, status, xhr) {
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
    
    $('body').off('click', '.btn-payment-update').on('click', '.btn-payment-update', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Payment');
        modal.find('.modal-body').load(url, function(response, status, xhr) {
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
    
    $('body').off('click', '.btn-payment-status-log').on('click', '.btn-payment-status-log', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Payment status log');
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
    .x_panel_payment {
        background-color: #d0e6ca;
    }
CSS;
$this->registerCss($css);
