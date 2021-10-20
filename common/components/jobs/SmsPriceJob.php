<?php

namespace common\components\jobs;

use sales\model\sms\useCase\UpdateSmsPrice;
use yii\queue\JobInterface;

/**
 * Class SmsPriceJob
 *
 * @property array $smsSids
 */
class SmsPriceJob extends BaseJob implements JobInterface
{
    public array $smsSids;

    public function execute($queue)
    {
        $this->waitingTimeRegister();
        try {
            (\Yii::createObject(UpdateSmsPrice::class))->update($this->smsSids);
        } catch (\Throwable $e) {
            \Yii::info($e->getMessage(), 'info\SmsPriceJob');
        }
    }
}
