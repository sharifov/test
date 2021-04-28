<?php

use common\models\Transaction;
use sales\auth\Auth;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var yii\web\View $this */
/* @var Transaction[] $transactions */
?>

<table class="table table-bordered table-hover table-striped">
    <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Code</th>
        <th>Type</th>
        <th>Amount</th>
        <th>Currency</th>
        <th>Comment</th>
        <th>Payment ID</th>
        <th></th>
    </tr>
    <?php foreach ($transactions as $transaction) : ?>
        <tr>
            <td>
                <?php if (Auth::can('/transaction/view')) : ?>
                    <?php echo Html::a($transaction->tr_id, ['/transaction/view', 'id' => $transaction->tr_id], ['target' => '_blank', 'data-pjax' => 0]) ?>
                <?php else : ?>
                    <?php echo $transaction->tr_id ?>
                <?php endif ?>
            </td>
            <td>
                <p style="white-space: nowrap;"><?php echo Html::encode($transaction->tr_date)?></p>
            </td>
            <td><?php echo Html::encode($transaction->tr_code) ?></td>
            <td><?php echo Transaction::getTypeName($transaction->tr_type_id) ?></td>
            <td><?php echo $transaction->tr_amount?></td>
            <td><?php echo Html::encode($transaction->tr_currency)?></td>
            <td><?php echo Html::encode($transaction->tr_comment)?></td>
            <td class="transaction_payment" data-payment="<?php echo $transaction->tr_payment_id ?>">
                <?php if (Auth::can('/payment/view')) : ?>
                    <?php echo Html::a($transaction->tr_payment_id, ['/payment/view', 'id' => $transaction->tr_payment_id], ['target' => '_blank', 'data-pjax' => 0]) ?>
                <?php else : ?>
                    <?php echo $transaction->tr_payment_id ?>
                <?php endif ?>
            </td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div class="dropdown-menu">
                        <?php
                        if (Auth::can('/order/transaction-actions/update')) {
                            echo Html::a(
                                '<i class="fa fa-edit text-warning" title="Update"></i> Update',
                                null,
                                [
                                    'class' => 'dropdown-item js-btn-transaction-update',
                                    'data-url' => Url::to([
                                        '/order/transaction-actions/update',
                                        'id' => $transaction->tr_id
                                    ])
                                ]
                            );
                        }
                        ?>
                        <?php
                        if (Auth::can('/order/transaction-actions/delete')) {
                            echo Html::a(
                                '<i class="glyphicon glyphicon-remove-circle text-danger" title="Delete"></i> Delete',
                                null,
                                [
                                    'class' => 'dropdown-item js-btn-transaction-delete',
                                    'data-url' => Url::to([
                                        '/order/transaction-actions/delete',
                                    ]),
                                    'data-id' => $transaction->tr_id,
                                    'data-order-id' => $transaction->trPayment->pay_order_id,
                                ]
                            );
                        }
                        ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
$js = <<<JS
    $('body').off('click', '.js-btn-transaction-delete').on('click', '.js-btn-transaction-delete', function (e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to delete this transaction?')) {
           return false;
        }
    
        let url = $(this).data('url');
        let transactionId = $(this).data('id');
        let orderId = $(this).data('order-id');
    
        $.ajax({
            url: url,
            type: 'post',
            data: {id: transactionId},
            dataType: 'json',
        })
        .done(function(dataResponse) {
            if (dataResponse.status === 1) {
                if ($('#pjax-order-transaction-' + orderId).length) {
                    pjaxReload({container: '#pjax-order-transaction-' + orderId});
                }
                createNotify('Success', 'Transaction was successfully deleted', 'success');
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.log({
                jqXHR : jqXHR,
                textStatus : textStatus,
                errorThrown : errorThrown
            });
            createNotify('Error', "Request failed: " + textStatus, 'error');
        })
        .always(function() {});
    });

    $('body').off('click', '.js-btn-transaction-update').on('click', '.js-btn-transaction-update', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Transaction');
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
    
    $(".transaction_payment").hover(function(){
        let paymentId = $(this).data('payment');
        let paymentRowEl = $('.payment_row_' + paymentId);
        if (paymentRowEl.length) {
            paymentRowEl.addClass('blinkOpacity');
        }
    }, function(){
        let paymentId = $(this).data('payment');
        let paymentRowEl = $('.payment_row_' + paymentId);
        if (paymentRowEl.length) {
            paymentRowEl.removeClass('blinkOpacity');
        }
    });
JS;
$this->registerJs($js);
?>
<?php
$css = <<<CSS
    .x_panel_transaction {
        background-color: #d0e6ca;
    }
    .blinkOpacity {
      animation: blinker 1s linear infinite;
    }    
    @keyframes blinker {
      50% {
        opacity: 0.2;
      }
    }
CSS;
$this->registerCss($css);
