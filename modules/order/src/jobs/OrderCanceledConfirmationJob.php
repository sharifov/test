<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\Order;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderCanceledConfirmationJob
 *
 * @property $orderId
 */
class OrderCanceledConfirmationJob implements RetryableJobInterface
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
            ], 'OrderCanceledConfirmationJob');
            return;
        }

        $projectId = $order->orLead->project_id ?? null;

        if (!$projectId) {
            \Yii::error([
                'message' => 'Not found Project',
                'orderId' => $this->orderId,
            ], 'OrderCanceledConfirmationJob');
            return;
        }

        $from = 'from.serge.murphy@techork.com';
        $fromName = '';
        $to = 'to.serge.murphy@techork.com';
        $toName = '';
        $templateKey = 'bwk_multi_product';
        $languageId = null;

        $mailPreview = \Yii::$app->communication->mailPreview(
            $projectId,
            $templateKey,
            $from,
            $to,
            (new EmailConfirmationData())->generate($order),
        );

        if ($mailPreview['error'] !== false) {
            throw new \DomainException($mailPreview['error']);
        }

        (new EmailConfirmationSender())->send(
            $order,
            $templateKey,
            $from,
            $fromName,
            $to,
            $toName,
            $languageId,
            $mailPreview['data']['email_subject'],
            $mailPreview['data']['email_body_html']
        );
    }

    public function getTtr(): int
    {
        return 1 * 60;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Order canceled confirmation error',
            'error' => $error->getMessage(),
            'orderId' => $this->orderId,
        ], 'OrderCanceledConfirmationJob');
        return !($attempt > 5);
    }
}
