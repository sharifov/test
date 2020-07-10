<?php

namespace common\components\jobs;

use common\components\ga\GaHelper;
use common\components\ga\GaLead;
use common\models\Lead;
use common\models\VisitorLog;
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
        /* TODO::
        create method in Lead - isReadyForGa

         */

        if (!$visitorLog = GaHelper::getLastGaClientIdByClient($this->lead->client_id)) { /* TODO:: check Tracking ID */
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