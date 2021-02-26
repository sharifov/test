<?php

namespace modules\order\src\payment\listeners;

use common\models\Payment;
use modules\order\src\entities\order\events\OrderPreparedEvent;
use modules\order\src\payment\jobs\ChargePaymentJob;

class PaymentChargeListener
{
    public function handle(OrderPreparedEvent $event): void
    {
        $payment = Payment::find()->andWhere(['pay_order_id' => $event->orderId])->one();
        if (!$payment) {
            \Yii::error([
                'message' => 'Payment charge error',
                'error' => 'Not found payment',
                'orderId' => $event->orderId,
            ], 'PaymentChargeListener');
            return;
        }
        \Yii::$app->queue_job->push(new ChargePaymentJob($payment->pay_id));
    }
}
