<?php

namespace common\components\jobs;

use common\components\ga\GaLead;
use common\models\Lead;
use common\models\VisitorLog;
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
                $gaLead->send();
            }
        } catch (\Throwable $throwable) {
            if ($throwable->getCode() < 0) {
                Yii::info(VarDumper::dumpAsString($throwable->getMessage()),
                'info\SendLeadInfoToGaJob:execute:Throwable');
            } else {
                Yii::error(VarDumper::dumpAsString($throwable->getMessage()),
                'SendLeadInfoToGaJob:execute:Throwable');
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function checkParams(): bool
    {
        if (!$visitorLog = VisitorLog::getLastGaClientIdByClient($this->lead->client_id)) {
            throw new \RuntimeException('Ga Client Id not found.', -10);
        }
        return true;
    }

    /**
     * @return float|int
     */
    public function getTtr()
    {
        return 1 * 20;
    }
}