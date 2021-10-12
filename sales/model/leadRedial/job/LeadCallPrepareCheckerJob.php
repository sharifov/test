<?php

namespace sales\model\leadRedial\job;

use common\components\jobs\BaseJob;
use common\models\Lead;
use sales\repositories\lead\LeadRepository;
use yii\queue\JobInterface;

/**
 * Class LeadCallPrepareCheckerJob
 *
 * @property int $leadId
 */
class LeadCallPrepareCheckerJob extends BaseJob implements JobInterface
{
    public int $leadId;

    public function __construct(int $leadId, ?float $timeStart = null, $config = [])
    {
        parent::__construct($timeStart, $config);
        $this->leadId = $leadId;
    }

    public function execute($queue)
    {
        $lead = Lead::findOne($this->leadId);

        if (!$lead) {
            return;
        }

        if (!$lead->isCallPrepare()) {
            return;
        }

        try {
            $lead->callReady();
            \Yii::createObject(LeadRepository::class)->save($lead);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'leadId' => $this->leadId,
            ], 'LeadCallPrepareCheckerJob');
        }
    }
}
