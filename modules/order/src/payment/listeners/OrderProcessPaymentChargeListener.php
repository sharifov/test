<?php

namespace modules\order\src\payment\listeners;

use common\models\Payment;
use modules\order\src\entities\order\events\OrderPreparedEvent;
use modules\order\src\payment\jobs\ChargePaymentJob;
use modules\order\src\processManager\OrderProcessManager;

class OrderProcessPaymentChargeListener
{
    public function handle(OrderPreparedEvent $event): void
    {
        $process = OrderProcessManager::findOne($event->orderId);

        if (!$process) {
            return;
        }

        if (!$process->isBooked()) {
            return;
        }

        $payment = Payment::find()->andWhere(['pay_order_id' => $event->orderId])->one();

        if (!$payment) {
            \Yii::error([
                'message' => 'Payment charge error',
                'error' => 'Not found payment',
                'orderId' => $event->orderId,
            ], 'OrderProcessPaymentChargeListener');
            return;
        }

        \Yii::$app->queue_job->push(new ChargePaymentJob($payment->pay_id));
    }
}
