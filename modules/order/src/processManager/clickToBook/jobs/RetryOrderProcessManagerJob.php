<?php

namespace modules\order\src\processManager\clickToBook\jobs;

use modules\order\src\processManager\clickToBook\commands\retryManager\Command;
use modules\order\src\processManager\clickToBook\commands\retryManager\Handler;
use yii\queue\JobInterface;

/**
 * Class RetryOrderProcessManagerJob
 * @package modules\order\src\processManager\clickToBook\jobs
 *
 * @property-read int $orderId
 */
class RetryOrderProcessManagerJob implements JobInterface
{
    private int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        try {
            $handler = \Yii::createObject(Handler::class);
            $handler->handle(new Command($this->orderId));
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'ClickToBook OrderProcess manager retry error',
                'error' => $e->getMessage(),
                'orderId' => $this->orderId,
            ], 'RetryOrderProcessManagerJob');
        }
    }
}
