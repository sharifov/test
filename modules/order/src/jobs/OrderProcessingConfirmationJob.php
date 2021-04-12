<?php

namespace modules\order\src\jobs;

use common\models\Notifications;
use modules\order\src\entities\order\Order;
use modules\order\src\services\confirmation\EmailConfirmationSender;
use sales\helpers\setting\SettingHelper;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderProcessingConfirmationJob
 *
 * @property $orderId
 */
class OrderProcessingConfirmationJob implements JobInterface
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
            ], 'OrderProcessingConfirmationJob');
            return;
        }

        try {
            (new EmailConfirmationSender(SettingHelper::getOrderProcessingEmailTemplateKey()))->sendWithoutAttachments($order);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Send Order Processing Confirmation Error',
                'error' => $e->getMessage(),
                'orderId' => $order->or_id,
            ], 'OrderProcessingConfirmationJob');
            if ($userId = ($order->orLead->employee_id ?? null)) {
                Notifications::createAndPublish(
                    $userId,
                    'Send Order Processing Confirmation Error',
                    'OrderId: ' . $order->or_id . ' Error: ' . $e->getMessage(),
                    Notifications::TYPE_DANGER,
                    true
                );
            }
        }
    }

//    public function getTtr(): int
//    {
//        return 1 * 60;
//    }
//
//    public function canRetry($attempt, $error): bool
//    {
//        \Yii::error([
//            'attempt' => $attempt,
//            'message' => 'Order processing confirmation error',
//            'error' => $error->getMessage(),
//            'orderId' => $this->orderId,
//        ], 'OrderProcessingConfirmationJob');
//        return !($attempt > 5);
//    }
}
