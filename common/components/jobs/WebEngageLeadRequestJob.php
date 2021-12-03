<?php

namespace common\components\jobs;

use common\models\Lead;
use modules\webEngage\src\service\webEngageEventData\lead\LeadEventDictionary;
use modules\webEngage\src\service\webEngageEventData\lead\LeadEventService;
use modules\webEngage\src\service\WebEngageRequestService;
use sales\helpers\app\AppHelper;
use Throwable;
use Yii;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * @property int|null $lead_id
 */
class WebEngageLeadRequestJob extends BaseJob implements JobInterface
{
    public $lead_id;

    /**
     * @param $queue
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();

        try {
            if (!$lead = LeadEventService::findLead($this->lead_id)) {
                throw new \RuntimeException('Lead not found by ID (' . $this->lead_id . ') ' .
                    ' and statuses (' . implode(',', LeadEventDictionary::STATUS_PROCESSED_LIST) . ')');
            }

            $data = (new LeadEventService($lead))->getData();
            $webEngageRequestService = new WebEngageRequestService();
            $webEngageRequestService->addEvent($data);
        } catch (Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'WebEngageLeadRequestJob:throwable'
            );
        }
    }
}
