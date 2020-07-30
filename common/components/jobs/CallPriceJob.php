<?php

namespace common\components\jobs;

use sales\model\call\useCase\UpdateCallPrice;
use yii\queue\JobInterface;

/**
 * Class CallPriceJob
 *
 * @property string $callSid
 */
class CallPriceJob implements JobInterface
{
    public string $callSid;

    public function execute($queue)
    {
        try {
            (\Yii::createObject(UpdateCallPrice::class))->update($this->callSid);
        } catch (\Throwable $e) {
            \Yii::info($e->getMessage(), 'info\CallPriceJob');
        }
    }
}
