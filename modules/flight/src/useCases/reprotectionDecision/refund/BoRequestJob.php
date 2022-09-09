<?php

namespace modules\flight\src\useCases\reprotectionDecision\refund;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use yii\queue\JobInterface;

/**
 * Class BoRequestJob
 *
 * @property string $bookingId
 * @property int $orderRefundId
 * @property int $productQuoteRefundId
 * @property int|null $userId
 * @property int $caseId
 */
class BoRequestJob extends BaseJob implements JobInterface
{
    public $bookingId;
    public $orderRefundId;
    public $productQuoteRefundId;
    public $userId;
    public int $caseId;

    public function execute($queue)
    {
        $this->waitingTimeRegister();
        try {
            $requestBo = \Yii::createObject(BoRequest::class);
            $requestBo->refund($this->bookingId, $this->orderRefundId, $this->productQuoteRefundId, $this->userId, $this->caseId);
        } catch (\Throwable $e) {
            \Yii::error(array_merge(['bookingId' => $this->bookingId], AppHelper::throwableLog($e, true)), 'BoRequestJob:reprotection:refund');
        }
    }
}
