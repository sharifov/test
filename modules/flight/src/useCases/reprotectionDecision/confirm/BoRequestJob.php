<?php

namespace modules\flight\src\useCases\reprotectionDecision\confirm;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use yii\queue\JobInterface;

/**
 * Class BoRequestJob
 *
 * @property string $quoteGid
 * @property int|null $userId
 */
class BoRequestJob extends BaseJob implements JobInterface
{
    public $quoteGid;
    public $userId;

    public function execute($queue)
    {
        $this->waitingTimeRegister();
        try {
            $requestBo = \Yii::createObject(BoRequest::class);
            $requestBo->appliedQuote($this->quoteGid, $this->userId);
        } catch (\Throwable $e) {
            \Yii::error(array_merge(['quoteGid' => $this->quoteGid], AppHelper::throwableLog($e, true)), 'BoRequestJob:reprotection:confirm');
        }
    }
}
