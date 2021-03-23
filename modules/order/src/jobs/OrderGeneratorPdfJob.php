<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\Order;
use modules\order\src\services\OrderPdfService;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;
use sales\helpers\app\AppHelper;
use sales\repositories\NotFoundException;

/**
 * Class OrderGeneratorPdfJob
 * @property int $orderId
 */
class OrderGeneratorPdfJob implements RetryableJobInterface
{
    public $orderId;

    /**
     * @param Queue $queue
     * @throws \Exception
     */
    public function execute($queue): void
    {
        \Yii::info([
            'message' => 'OrderGeneratorPdfJob is started',
            'orderId' => $this->orderId,
        ], 'info\OrderGeneratorPdfJob:run');

        try {
            if (!$order = Order::findOne(['or_id' => $this->orderId])) {
                throw new NotFoundException('Order not found. Id (' . $this->orderId . ')');
            }
            if ((new OrderPdfService($order))->processingFile()) {
                \Yii::info([
                    'message' => 'OrderGeneratorPdfJob - file is generated',
                    'orderId' => $this->orderId,
                ], 'info\OrderGeneratorPdfJob:success');
            }
        } catch (NotFoundException $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'OrderGeneratorPdfJob:Execute:Throwable'
            );
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'OrderGeneratorPdfJob:Execute:Throwable'
            );
            throw new \Exception($throwable->getMessage());
        }
    }

    /**
     * @return int time to reserve in seconds
     */
    public function getTtr(): int
    {
        return 2 * 60;
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
