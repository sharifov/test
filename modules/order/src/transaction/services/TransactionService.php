<?php

namespace modules\order\src\transaction\services;

use common\models\Transaction;

/**
 * Class TransactionService
 */
class TransactionService
{
    /**
     * @param int $orderId
     * @return Transaction[]
     */
    public static function getTransactionsByOrder(int $orderId): array
    {
        return Transaction::find()
            ->innerJoinWith('trPayment', false)
            ->andWhere(['pay_order_id' => $orderId])
            ->orderBy([
                'tr_payment_id' => SORT_ASC,
                'tr_id' => SORT_ASC,
            ])
            ->all();
    }
}
