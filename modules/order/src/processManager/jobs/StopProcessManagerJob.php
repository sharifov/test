<?php

namespace modules\order\src\processManager\jobs;

use modules\order\src\processManager\OrderProcessManagerCanceler;
use yii\queue\JobInterface;

/**
 * Class StopProcessManagerJob
 *
 * @property int $orderId
 */
class StopProcessManagerJob implements JobInterface
{
    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        try {
            $canceler = \Yii::createObject(OrderProcessManagerCanceler::class);
            $canceler->stop($this->orderId);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Stop Order Process Manager error',
                'error' => $e->getMessage(),
                'orderId' => $this->orderId,
            ], 'StopProcessManagerJob');
        }
    }
}
