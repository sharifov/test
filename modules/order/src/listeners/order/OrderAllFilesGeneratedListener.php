<?php

namespace modules\order\src\listeners\order;

use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\order\src\events\OrderFileGeneratedEvent;
use modules\order\src\jobs\OrderSendCompletedConfirmationJob;
use modules\product\src\entities\productQuote\ProductQuote;

class OrderAllFilesGeneratedListener
{
    public function handle(OrderFileGeneratedEvent $event): void
    {
        $receipt = FileOrder::find()->andWhere([
            'fo_category_id' => FileOrder::CATEGORY_RECEIPT,
            'fo_or_id' => $event->orderId,
        ])->exists();

        if (!$receipt) {
            return;
        }

        $quotes = ProductQuote::find()->select(['pq_id'])->andWhere(['pq_order_id' => $event->orderId])->column();
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

        \Yii::$app->queue_job->push(new OrderSendCompletedConfirmationJob($event->orderId));
    }
}
