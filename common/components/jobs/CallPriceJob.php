<?php

namespace common\components\jobs;

use sales\model\call\useCase\UpdateCallPrice;
use yii\queue\JobInterface;

/**
 * Class CallPriceJob
 *
 * @property array $callSids
 */
class CallPriceJob extends BaseJob implements JobInterface
{
    public array $callSids = [];

    public function execute($queue)
    {
        $this->executionTimeRegister();
        try {
            (\Yii::createObject(UpdateCallPrice::class))->update($this->callSids);
        } catch (\Throwable $e) {
            \Yii::info($e->getMessage(), 'info\CallPriceJob');
        }
    }
}
