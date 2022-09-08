<?php

namespace modules\flight\src\useCases\reprotectionDecision\modify;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use yii\queue\JobInterface;

/**
 * Class BoRequestJob
 *
 * @property string $quoteGid
 * @property int|null $userId
 * @property int $caseId
 */
class BoRequestJob extends BaseJob implements JobInterface
{
    public $quoteGid;
    public $userId;
    public int $caseId;

    public function execute($queue)
    {
        $this->waitingTimeRegister();
        try {
            $requestBo = \Yii::createObject(BoRequest::class);
            $requestBo->appliedQuote($this->quoteGid, $this->userId, $this->caseId);
        } catch (\Throwable $e) {
            \Yii::error(array_merge(['quoteGid' => $this->quoteGid], AppHelper::throwableLog($e, true)), 'BoRequestJob:reprotection:modify');
        }
    }
}
