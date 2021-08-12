<?php

namespace modules\flight\src\useCases\reprotectionDecision\refund;

use common\components\jobs\BaseJob;
use sales\helpers\app\AppHelper;
use yii\queue\JobInterface;

/**
 * Class BoRequestJob
 *
 * @property string $bookingId
 * @property int $orderRefundId
 * @property int $productQuoteRefundId
 */
class BoRequestJob extends BaseJob implements JobInterface
{
    public $bookingId;
    public $orderRefundId;
    public $productQuoteRefundId;

    public function execute($queue)
    {
        $this->executionTimeRegister();
        try {
            $requestBo = \Yii::createObject(BoRequest::class);
            $requestBo->refund($this->bookingId, $this->orderRefundId, $this->productQuoteRefundId);
        } catch (\Throwable $e) {
            \Yii::error(array_merge(['bookingId' => $this->bookingId], AppHelper::throwableLog($e, true)), 'BoRequestJob:reprotection:refund');
        }
    }
}
