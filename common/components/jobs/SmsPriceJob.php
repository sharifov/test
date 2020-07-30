<?php

namespace common\components\jobs;

use sales\model\sms\useCase\UpdateSmsPrice;
use yii\queue\JobInterface;

/**
 * Class SmsPriceJob
 *
 * @property string $smsSid
 */
class SmsPriceJob implements JobInterface
{
    public string $smsSid;

    public function execute($queue)
    {
        try {
            (\Yii::createObject(UpdateSmsPrice::class))->update($this->smsSid);
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage(), 'SmsPriceJob');
        }
    }
}
