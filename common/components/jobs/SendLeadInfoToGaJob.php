<?php

namespace common\components\jobs;

use common\components\ga\GaLead;
use common\models\Lead;
use src\helpers\app\AppHelper;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 *
 * @property float|int $ttr
 */
class SendLeadInfoToGaJob extends BaseJob implements JobInterface
{
    public Lead $lead;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->waitingTimeRegister();
        $this->timeExecution = microtime(true);
        try {
            if ($this->checkParams() && $gaLead = new GaLead($this->lead)) {
                $response = $gaLead->send();
                if ($response) {
                    /*Yii::info(
                        [
                            'leadId' => $this->lead->id,
                            'message' => 'Info sent to GA',
                            'data' => $gaLead->getPostData()
                        ],
                        'info\SendLeadInfoToGaJob:execute:sent'
                    );*/
                } else {
                    Yii::warning(
                        [
                            'leadId' => $this->lead->id,
                            'message' => 'Info NOT sent to GA',
                            'data' => $gaLead->getPostData()
                        ],
                        'SendLeadInfoToGaJob:execute:sent'
                    );
                }
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'SendLeadInfoToGaJob:execute:Throwable');
        }

        $this->execTimeRegister();

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
