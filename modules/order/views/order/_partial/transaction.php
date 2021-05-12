<?php

use common\models\Payment;
use common\models\Transaction;
use modules\order\src\entities\order\Order;
use modules\order\src\transaction\services\TransactionService;
use sales\auth\Auth;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var Order $order */
?>
<?php
    $transactions = TransactionService::getTransactionsByOrder($order->or_id);
?>
<div class="order-view-transaction-box">
    <?php Pjax::begin(['id' => 'pjax-order-transaction-' . $order->or_id, 'enablePushState' => false, 'timeout' => 10000])?>

        <div class="x_panel x_panel_transaction">
            <div class="x_title">
                <h2><i class="fa fa-exchange"></i> Transaction List <sup>(<?php echo count($transactions) ?>)</sup></h2>
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
                    <div class="x_content" style="display: <?php echo $transactions ? 'block' : 'none' ?>">
                        <?php if ($transactions) : ?>
                            <?php echo $this->render('@frontend/views/transaction/_partial/_transaction_table', [
                                'transactions' => $transactions,
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php Pjax::end() ?>
</div>

