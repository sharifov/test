<?php

namespace common\components\jobs;

use common\components\ga\GaLead;
use common\models\Lead;
use sales\helpers\app\AppHelper;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 *
 * @property float|int $ttr
 */
class SendLeadInfoToGaJob extends BaseObject implements JobInterface
{
    public Lead $lead;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue) : bool
    {
        try {
            if($this->checkParams() && $gaLead = new GaLead($this->lead)) {

                if ($response = $gaLead->send()) {
                    Yii::info(VarDumper::dumpAsString($response->content),
                    'info\SendLeadInfoToGaJob:response'); /* TODO:: FOR DEBUG:: must by remove  */
                }
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'SendLeadInfoToGaJob:execute:Throwable');
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function checkParams(): bool
    {
        return $this->lead->isReadyForGa();
    }

    /**
     * @return float|int
     */
    public function getTtr()
    {
        return 1 * 20;
    }
}