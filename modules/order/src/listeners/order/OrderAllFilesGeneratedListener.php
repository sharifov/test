<?php

namespace modules\order\src\listeners\order;

use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\order\src\events\OrderFileGeneratedEvent;
use modules\order\src\jobs\OrderSendCompletedConfirmationJob;
use modules\order\src\processManager\queue\Queue;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class OrderAllFilesGeneratedListener
 *
 * @property Queue $queue
 */
class OrderAllFilesGeneratedListener
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(OrderFileGeneratedEvent $event): void
    {
        $receipt = FileOrder::find()->andWhere([
            'fo_category_id' => FileOrder::CATEGORY_RECEIPT,
            'fo_or_id' => $event->orderId,
        ])->exists();

        if (!$receipt) {
            return;
        }

        $quotes = ProductQuote::find()->select(['pq_id'])->byOrderId($event->orderId)->booked()->column();
        foreach ($quotes as $quote) {
            $confirm = FileOrder::find()->andWhere([
                'fo_category_id' => FileOrder::CATEGORY_CONFIRMATION,
                'fo_or_id' => $event->orderId,
                'fo_pq_id' => $quote
            ])->exists();

            if (!$confirm) {
                return;
            }
        }

        if (!$this->isSent($event->orderId)) {
            $this->queue->push(new OrderSendCompletedConfirmationJob($event->orderId));
        }
    }

    private function isSent(int $orderId): bool
    {
        $key = 'OrderSendCompletedConfirmation_' . $orderId;
        $result = (bool)\Yii::$app->redis->setnx($key, date('Y-m-d H:i:s'));
        if (!$result) {
            return true;
        }
        $expireSeconds = 600;
        \Yii::$app->redis->expire($key, $expireSeconds);
        return false;
    }
}
