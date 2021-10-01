<?php

namespace sales\model\leadRedial\job;

use common\components\jobs\BaseJob;
use common\models\Lead;
use sales\helpers\setting\SettingHelper;
use sales\model\leadRedial\assign\LeadRedialMultiAssigner;
use Yii;
use yii\queue\JobInterface;

/**
 * Class LeadRedialAssignToUsersJob
 * @package sales\model\leadRedial\job
 *
 * @property int $leadId
 * @property int $agentsLimit
 */
class LeadRedialAssignToUsersJob extends BaseJob implements JobInterface
{
    public int $leadId;

    public int $agentsLimit;

    public function __construct(int $leadId, int $agentsLimit, ?float $timeStart = null, $config = [])
    {
        parent::__construct($timeStart, $config);
        $this->leadId = $leadId;
        $this->agentsLimit = $agentsLimit;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $lead = Lead::findOne($this->leadId);

        if ($lead) {
            $assigner = Yii::createObject(LeadRedialMultiAssigner::class);
            $isAssigned = $assigner->assign($lead, $this->agentsLimit, new \DateTimeImmutable());
            if ($isAssigned) {
                Yii::$app->queue_job->delay(SettingHelper::getRedialUserAccessExpiredSecondsLimit())->push(new LeadRedialExpiredAccessJob($lead->id));
            }
        }
    }
}
