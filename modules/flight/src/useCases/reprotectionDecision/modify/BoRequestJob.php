<?php

namespace modules\flight\src\useCases\reprotectionDecision\modify;

use common\components\jobs\BaseJob;
use sales\helpers\app\AppHelper;
use yii\queue\JobInterface;

/**
 * Class BoRequestJob
 *
 * @property string $quoteGid
 */
class BoRequestJob extends BaseJob implements JobInterface
{
    public $quoteGid;

    public function execute($queue)
    {
        $this->executionTimeRegister();
        try {
            $requestBo = \Yii::createObject(BoRequest::class);
            $requestBo->appliedQuote($this->quoteGid);
        } catch (\Throwable $e) {
            \Yii::error(array_merge(['quoteGid' => $this->quoteGid], AppHelper::throwableLog($e, true)), 'BoRequestJob:reprotection:modify');
        }
    }
}
