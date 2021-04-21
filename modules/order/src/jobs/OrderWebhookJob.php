<?php

namespace modules\order\src\jobs;

use common\components\BackOffice;
use modules\order\src\entities\order\Order;
use sales\helpers\app\AppHelper;
use yii\helpers\VarDumper;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderWebhookJob
 * @package modules\order\src\jobs
 *
 * @property int $orderId
 */
class OrderWebhookJob implements RetryableJobInterface
{
    private int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        \Yii::info('send bo webhook order id: ' . $this->orderId, 'info\OrderWebhookJob');

        $order = Order::findOne(['or_id' => $this->orderId]);

        if ($order) {
            $data = [
                'fareId' => $order->or_fare_id
            ];
            BackOffice::orderUpdateWebhook($data);
        } else {
            \Yii::error('Can not send webhook to BO: Order is not found', 'OrderWebhookJob::execute');
        }
    }

    /**
     * @return int time to reserve in seconds
     */
    public function getTtr(): int
    {
        return 1;
    }

    /**
     * @param int $attempt number
     * @param \Exception|\Throwable $error from last execute of the job
     * @return bool
     */
    public function canRetry($attempt, $error): bool
    {
        return ($attempt < 3);
    }
}
