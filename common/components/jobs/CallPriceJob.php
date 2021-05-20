<?php

namespace common\components\jobs;

use sales\model\call\useCase\UpdateCallPrice;
use yii\queue\JobInterface;

/**
 * Class CallPriceJob
 *
 * @property array $callSids
 */
class CallPriceJob implements JobInterface
{
    public array $callSids = [];

    public function execute($queue)
    {
        try {
            (\Yii::createObject(UpdateCallPrice::class))->update($this->callSids);
        } catch (\Throwable $e) {
            \Yii::info($e->getMessage(), 'info\CallPriceJob');
        }
    }
}
