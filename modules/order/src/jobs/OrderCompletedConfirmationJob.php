<?php

namespace modules\order\src\jobs;

use common\models\Notifications;
use modules\order\src\entities\order\Order;
use modules\order\src\services\confirmation\EmailConfirmationSender;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderCompletedConfirmationJob
 *
 * @property $orderId
 */
class OrderCompletedConfirmationJob implements RetryableJobInterface
{
    public $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        $order = Order::findOne($this->orderId);

        if (!$order) {
            \Yii::error([
                'message' => 'Not found Order',
                'orderId' => $this->orderId,
            ], 'OrderCompletedConfirmationJob');
            return;
        }

        try {
            (new EmailConfirmationSender())->sendWithAttachments($order);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Send Order Completed Confirmation Error',
                'error' => $e->getMessage(),
                'orderId' => $order->or_id,
            ], 'OrderCompletedConfirmationJob');
            if ($userId = ($order->orLead->employee_id ?? null)) {
                Notifications::createAndPublish(
                    $userId,
                    'Send Order Completed Confirmation Error',
                    'OrderId: ' . $order->or_id . ' Error: ' . $e->getMessage(),
                    Notifications::TYPE_DANGER,
                    true
                );
            }
        }
    }

    public function getTtr(): int
    {
        return 1 * 60;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Order completed confirmation error',
            'error' => $error->getMessage(),
            'orderId' => $this->orderId,
        ], 'OrderCompletedConfirmationJob');
        return !($attempt > 5);
    }
}
