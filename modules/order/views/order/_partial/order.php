<?php

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\processManager\phoneToBook\OrderProcessManager;
use modules\order\src\processManager\Status;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use sales\auth\Auth;
use sales\helpers\product\ProductQuoteHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\widgets\DetailView;

/* @var yii\web\View $this */
/* @var Order $order */
/* @var OrderProcessManager|null $orderProcessManage */

$formatter = new \common\components\i18n\Formatter();
?>

<div class="order-view-box">
    <?php Pjax::begin(['id' => 'pjax-order-view-' . $order->getId(), 'timeout' => 10000]) ?>
    <div class="x_panel x_panel_orders">
        <?= DetailView::widget([
                'model' => $order,
                'attributes' => [
                    [
                        'label' => 'Owner',
                        'value' => static function (Order $model) {
                            return !empty($model->orOwnerUser->username) ? '<i class="fa fa-user"></i> ' . $model->orOwnerUser->username : ' - ';
                        },
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'Language',
                        'value' => static function (Order $model) {
                            return !empty($model->orderData->language->language_id) ? '<i class="fa fa-language"></i> ' . $model->orderData->language->language_id : ' - ';
                        },
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'Market Country',
                        'value' => static function (Order $model) {
                            return !empty($model->orderData->od_market_country) ? '<i class="fa fa-globe"></i> ' . $model->orderData->od_market_country : ' - ';
                        },
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'Source',
                        'value' => static function (Order $model) {
                            return !empty($model->orderData->source->name) ? '<i class="fa fa-bookmark-o"></i> ' . $model->orderData->source->name : ' - ';
                        },
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'Fare ID',
                        'value' => static function (Order $model) {
                            return !empty($model->or_fare_id) ? '<i class="fa fa-fire"></i> ' . $model->or_fare_id : ' - ';
                        },
                        'format' => 'raw'
                    ],
                ],
            ])
?>
    </div>

        <!--<div class="x_panel x_panel_orders">
            <?php /*echo !empty($order->orOwnerUser->username) ? '<span title="Owner"><i class="fa fa-user"></i> ' . $order->orOwnerUser->username  . ' <strong>|</strong><span> ' : null */?>
            <?php /*echo !empty($order->orderData->language->language_id) ? '<span title="Language"><i class="fa fa-language"></i> ' . $order->orderData->language->language_id . ' <strong>|</strong><span> ' : null*/?>
            <?php /*echo !empty($order->orderData->od_market_country) ? '<span title="Market Country"><i class="fa fa-globe"></i> ' . $order->orderData->od_market_country . ' <strong>|</strong><span> ' : null */?>
            <?php /*echo !empty($order->orderData->source->name) ? '<span title="Source"><i class="fa fa-bookmark-o"></i> ' . $order->orderData->source->name . ' <strong>|</strong><span> ' : null*/?>
            <?php /*echo !empty($order->or_fare_id) ? '<span title="Fare ID"><i class="fa fa-fire"></i> ' . $order->or_fare_id . ' <strong>|</strong><span> ' : null*/?>
        </div>-->

        <div class="x_panel x_panel_orders">
            <div class="x_title">
                <h2>
                    <i class="fas fa-money-check-alt"></i>&nbsp;
                        Order GID (<?php echo $order->or_gid ?>)&nbsp;
                        Name (<?php echo $order->or_name ?>)
                </h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">
                <div class="x_panel">
                    <div class="x_title">

                        <small><span class="badge badge-white">OR<?=($order->or_id)?></span></small>
                        (<span title="GID: <?=\yii\helpers\Html::encode($order->or_gid)?>"><?=\yii\helpers\Html::encode($order->or_uid)?></span>)
                        <?= OrderStatus::asFormat($order->or_status_id) ?>
                        <?= OrderPayStatus::asFormat($order->or_pay_status_id) ?>
                        <?= $order->or_project_id ? $formatter->asProjectName($order->or_project_id) : null ?>
                        "<b><?=\yii\helpers\Html::encode($order->or_name)?></b>"

                        <?php if ($order->or_profit_amount > 0) : ?>
                            <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $order->or_profit_amount ?>
                        <?php endif; ?>
                        <?php if ($orderProcessManage) : ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;Auto Process: (<?= Status::LIST[$orderProcessManage->opm_status] ?? 'undefined'?>)
                        <?php endif; ?>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <?php if (Auth::can('/order/order-user-profit/ajax-manage-order-user-profit')) : ?>
                                    <?= Html::a('<i class="fas fa-dollar-sign text-success"></i> Split Profit', null, [
                                        'class' => 'text-success btn-split',
                                        'data-url' => Url::to(['/order/order-user-profit/ajax-manage-order-user-profit']),
                                        'data-order-id' => $order->or_id,
                                        'data-title' => 'Order User Profit',
                                    ]) ?>
                                <?php endif ?>
                            </li>

                            <?php if ($order->orderTips && Auth::can('/order/order-tips-user-profit/ajax-manage-order-tips-user-profit')) : ?>
                                <li>
                                    <?= Html::a('<i class="fas fa-dollar-sign text-success"></i> Split Tips', null, [
                                        'class' => 'text-success btn-split',
                                        'data-url' => Url::to(['/order/order-tips-user-profit/ajax-manage-order-tips-user-profit']),
                                        'data-order-id' => $order->or_id,
                                        'data-title' => 'Order User Tips',
                                    ]) ?>
                                </li>
                            <?php endif; ?>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle text-warning" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars"></i> Actions</a>
                                <div class="dropdown-menu" role="menu">
                                    <?php if ($orderProcessManage) : ?>
                                        <?php if ($orderProcessManage->isRunning()) : ?>
                                            <?php if (Auth::can('/order/order-process-actions/cancel-process')) : ?>
                                                <?= Html::a('Cancel Auto Process', null, [
                                                    'data-url' => Url::to(['/order/order-process-actions/cancel-process']),
                                                    'class' => 'dropdown-item btn-cancel-process',
                                                    'data-order-id' => $order->or_id,
                                                ])?>
                                            <?php endif;?>
                                        <?php endif;?>
                                    <?php else : ?>
                                        <?php if (Auth::can('/order/order-process-actions/start-process')) : ?>
                                            <?= Html::a('Start Auto Processing', null, [
                                                'data-url' => Url::to(['/order/order-process-actions/start-process']),
                                                'class' => 'dropdown-item btn-start-process',
                                                'data-order-id' => $order->or_id,
                                            ])?>
                                        <?php endif;?>
                                    <?php endif;?>

                                    <?php if (Auth::can('/order/order-actions/cancel') && !$order->isCanceled()) : ?>
                                        <?= Html::a('Cancel Order', null, [
                                            'data-url' => Url::to(['/order/order-actions/cancel', 'orderId' => $order->or_id]),
                                            'class' => 'dropdown-item btn-cancel-order'
                                        ])?>
                                    <?php endif ?>

                                    <?php if (Auth::can('/order/order-actions/complete') && !$order->isComplete()) : ?>
                                        <?= Html::a('Complete Order', null, [
                                            'data-url' => Url::to(['/order/order-actions/complete', 'orderId' => $order->or_id]),
                                            'class' => 'dropdown-item btn-complete-order'
                                        ])?>
                                    <?php endif ?>

                                    <?php if (Auth::can('/order/order-actions/send-email-confirmation')) : ?>
                                        <?= Html::a('Send Email Confirmation', null, [
                                            'data-url' => Url::to(['/order/order-actions/send-email-confirmation']),
                                            'data-id' => $order->or_id,
                                            'class' => 'dropdown-item btn-order-send-email-confirmation'
                                        ])?>
                                    <?php endif ?>

                                    <?php if (Auth::can('/order/order-actions/generate-files')) : ?>
                                        <?= Html::a('<i class="fa fa-file-pdf-o"></i> Generate PDF', null, [
                                            'data-url' => Url::to(['/order/order-actions/generate-files']),
                                            'data-id' => $order->or_id,
                                            'class' => 'dropdown-item btn-order-generate-files'
                                        ])?>
                                    <?php endif ?>

                                    <?php if (Auth::can('/order/order/update-ajax')) : ?>
                                        <?= Html::a('<i class="fa fa-edit"></i> Update order', null, [
                                            'data-url' => Url::to(['/order/order/update-ajax', 'id' => $order->or_id]),
                                            'class' => 'dropdown-item text-warning btn-update-order'
                                        ])?>
                                    <?php endif ?>

                                    <?php if (Auth::can('/order/order-status-log/show')) : ?>
                                        <?= Html::a('<i class="fa fa-list"></i> Status log', null, [
                                            'class' => 'dropdown-item btn-order-status-log',
                                            'data-url' => Url::to(['/order/order-status-log/show', 'gid' => $order->or_gid]),
                                            'data-gid' => $order->or_gid,
                                        ]) ?>
                                    <?php endif ?>

                                    <div class="dropdown-divider"></div>
                                    <?php if (Auth::can('/order/order/delete-ajax')) : ?>
                                        <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete order', null, [
                                            'class' => 'dropdown-item text-danger btn-delete-order',
                                            'data-order-id' => $order->or_id,
                                            'data-url' => Url::to(['/order/order/delete-ajax']),
                                        ]) ?>
                                    <?php endif ?>
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
                            $calcTotalPrice = 0;
                            $orderTipsAmount = 0.00;
                        ?>

                        <table class="table table-bordered">
                            <?php if ($order->productQuotes) :
                                $nr = 1;
                                ?>
                                <tr>
                                    <th>Nr</th>
                                    <th>Product</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th title="Options, USD">Options, USD</th>
                                    <th title="Service FEE">FEE</th>
                                    <th title="Origin Price, USD">Price, USD</th>
                                    <th>Client Price</th>
                                    <th></th>
                                </tr>
                                <?php foreach ($order->productQuotes as $productQuote) :
                                    $quote = $productQuote;
                                    $ordTotalPrice += $quote->pq_price;
                                    $ordTotalFee += $quote->pq_service_fee_sum;
                                    $ordClientTotalPrice += $quote->pq_client_price;
                                    $ordOptionTotalPrice += $quote->optionAmountSum;
                                    ?>
                                    <tr>
                                        <td title="Product Quote ID: <?=Html::encode($quote->pq_id)?>"><?= $nr++ ?> <br> <?= ProductQuoteHelper::displayOriginOrAlternativeIcon($productQuote) ?></td>
                                        <td title="<?=Html::encode($quote->pq_product_id)?>">
                                            <?= $quote->pqProduct->prType->pt_icon_class ? Html::tag('i', '', ['class' => $quote->pqProduct->prType->pt_icon_class]) : '' ?>
                                            <?=Html::encode($quote->pqProduct->prType->pt_name)?>
                                            <?=$quote->pqProduct->pr_name ? ' - ' . Html::encode($quote->pqProduct->pr_name) : ''?>
                                        </td>
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
                                                'data-url' => Url::to(['/order/order-product/delete-ajax'])
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

                                    $orderTipsAmount = $order->orderTips ? $order->orderTips->ot_amount : 0.00;
                                    $orderTipsAmountClient = $order->orderTips ? $order->orderTips->ot_client_amount : 0.00;

                                    $calcTotalPrice = round($ordTotalPrice + $ordOptionTotalPrice + $orderTipsAmount, 2);
                                    $calcClientTotalPrice = round(($calcTotalPrice) * $order->or_client_currency_rate, 2);

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
                                    <th class="text-right" colspan="5">Tips: </th>
                                    <td class="text-center" colspan="2">(DB)</td>
                                    <th class="text-right"><?=number_format($orderTipsAmount, 2)?></th>
                                    <th class="text-right"><?=number_format($orderTipsAmountClient, 2)?> <?=Html::encode($order->or_client_currency)?></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="5">Calc Total: </th>
                                    <td class="text-center" colspan="2">(price + opt + tips)</td>
                                    <th class="text-right"><?=number_format($calcTotalPrice, 2)?></th>
                                    <th class="text-right"><?=number_format($calcClientTotalPrice, 2)?> <?=Html::encode($order->or_client_currency)?></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="5">Total: </th>
                                    <td class="text-center" colspan="2">(DB + Tips)</td>
                                    <th class="text-right"><?=number_format($order->or_app_total + $orderTipsAmount, 2)?></th>
                                    <th class="text-right"><?=number_format($order->or_client_total + $orderTipsAmountClient, 2)?> <?=Html::encode($order->or_client_currency)?></th>
                                    <th></th>
                                </tr>
                            <?php endif; ?>
                        </table>

                        <i class="fa fa-user"></i> <?=$order->orOwnerUser ? Html::encode($order->orOwnerUser->username) : '-'?>,
                        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($order->or_created_dt)) ?>,
                        <i class="fa fa-money" title="currency"></i> <?=Html::encode($order->or_client_currency)?> <span title="Rate: <?=$order->or_client_currency_rate?>">(<?=round($order->or_client_currency_rate, 3)?>)</span>

                        <div class="text-right"><h4>Calc Total: <?=number_format($order->orderTotalCalcSum  + $orderTipsAmount, 2)?> USD, Total: <?=number_format($order->or_app_total + $orderTipsAmount, 2)?> USD</h4></div>
                    </div>
                </div>
            </div>
        </div>
    <?php Pjax::end() ?>
</div>

<?php
$js = <<<JS
    $('body').off('click', '.btn-split').on('click', '.btn-split', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let orderId = $(this).data('order-id');
        let title = $(this).data('title');
    
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html(title);
        modal.find('.modal-body').load(url, {orderId: orderId}, function( response, status, xhr ) {
            if (status === 'error') {
                let message = xhr.status === 403 ? xhr.responseText : 'Internal Server Error.';
                createNotify('Error', message, 'error');
            } else {
                modal.modal({
                  show: true
                });
            }
        });
    });
    
    $('body').off('click', '.btn-cancel-process').on('click', '.btn-cancel-process', function(e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to cancel this order process?')) {
            return false;
        }
        
        let orderId = $(this).data('order-id');
        let url = $(this).data('url');
        let pjaxBoxId = '#pjax-order-view-' + orderId;
        $('#preloader').removeClass('d-none');
        
        $.ajax({
            url: url,
            type: 'post',
            data: {'id': orderId},
            dataType: 'json'
        })
        .done(function(data) {
            if (data.error) {
                createNotify('Error: cancel order process', data.message, 'error');
            } else {
                $.pjax.reload({container: pjaxBoxId, push: false, replace: false, async: false, timeout: 2000});
                createNotify('The order process was successfully canceled', data.message, 'success');
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
        
    $('body').off('click', '.btn-start-process').on('click', '.btn-start-process', function(e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to start this order process?')) {
            return false;
        }
        
        let orderId = $(this).data('order-id');
        let url = $(this).data('url');
        let pjaxBoxId = '#pjax-order-view-' + orderId;
        $('#preloader').removeClass('d-none');
        
        $.ajax({
            url: url,
            type: 'post',
            data: {'id': orderId},
            dataType: 'json'
        })
        .done(function(data) {
            if (data.error) {
                createNotify('Error: start order process', data.message, 'error');
            } else {
                $.pjax.reload({container: pjaxBoxId, push: false, replace: false, async: false, timeout: 2000});
                createNotify('The order process was successfully started', data.message, 'success');
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
    
    $('body').off('click', '.btn-complete-order').on('click', '.btn-complete-order', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Complete Order');
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
    
    $('body').off('click', '.btn-cancel-order').on('click', '.btn-cancel-order', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Cancel Order');
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
    
    $('body').off('click', '.btn-order-send-email-confirmation').on('click', '.btn-order-send-email-confirmation', function(e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to send email confirmation?')) {
            return false;
        }
        
        let orderId = $(this).data('id');
        let url = $(this).data('url');
        $('#preloader').removeClass('d-none');
      
        $.ajax({
            url: url,
            type: 'post',
            data: {'id': orderId},
            dataType: 'json'
        })
        .done(function(data) {
            if (data.error) {
                createNotify('Error: send email confirmation', data.message, 'error');
            } else {
                createNotify('The email was successfully sent', 'Success', 'success');
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
        
    $('body').off('click', '.btn-order-generate-files').on('click', '.btn-order-generate-files', function(e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to generate file?')) {
            return false;
        }
        
        let orderId = $(this).data('id');
        let url = $(this).data('url');
        $('#preloader').removeClass('d-none');
        
        $.ajax({
            url: url,
            type: 'post',
            data: {'id': orderId},
            dataType: 'json'
        })
        .done(function(data) {
            if (data.error) {
                createNotify('Error: file generating', data.message, 'error');
            } else {
                pjaxReload({container: '#pjax-order-file-' + orderId}); 
                createNotify('File generated', 'Success', 'success');
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
    
    $('body').off('click', '.btn-update-order').on('click', '.btn-update-order', function (e) {
        e.preventDefault();
        let url = $(this).data('url');

        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update order');
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
    
    $('body').off('click', '.btn-order-status-log').on('click', '.btn-order-status-log', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let gid = $(this).data('gid');
        
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Order [' + gid + '] status history');
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
    
    $('body').off('click', '.btn-delete-order').on('click', '.btn-delete-order', function(e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to delete this order?')) {
            return false;
        }

        let orderId = $(this).data('order-id');
        let url = $(this).data('url');
        $('#preloader').removeClass('d-none');     

        $.ajax({
            url: url,
            type: 'post',
            data: {'id': orderId},
            dataType: 'json'
        })
        .done(function(data) {
            if (data.error) {
                createNotify('Error', data.error, 'error');
            } else {
                createNotify('The order was successfully removed', 'Success', 'success');
                $(location).attr('href', '/order/order/search');
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
    
    $('body').off('click', '.btn-delete-quote-from-order').on('click', '.btn-delete-quote-from-order', function (e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to delete this quote from order?')) {
            return false;
        }
        
        let menu = $(this);
        let productQuoteId = menu.data('product-quote-id');
        let orderId = menu.data('order-id');
        let url = menu.data('url');
        $('#preloader').removeClass('d-none');
        
        $.ajax({
            url: url,
            type: 'post',
            data: {'product_quote_id': productQuoteId, 'order_id': orderId},
            dataType: 'json'
        })
        .done(function(data) {
            if (data.error) {
                 createNotify('Error: delete quote from order', data.error, 'error');
            } else {
                pjaxReload({container: '#pjax-order-view-' + orderId});
                createNotify('Quote was successfully deleted', data.message, 'success');
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
JS;
$this->registerJs($js, yii\web\View::POS_END);

$css = <<<CSS
    .x_panel_orders {
       background-color: #cad7e4;
    }
CSS;
$this->registerCss($css);
