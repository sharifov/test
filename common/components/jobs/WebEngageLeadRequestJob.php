<?php

namespace common\components\jobs;

use common\models\Lead;
use modules\webEngage\form\WebEngageEventForm;
use modules\webEngage\src\service\webEngageEventData\lead\LeadEventDictionary;
use modules\webEngage\src\service\webEngageEventData\lead\LeadEventService;
use modules\webEngage\src\service\WebEngageRequestService;
use sales\helpers\app\AppHelper;
use Throwable;
use Yii;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * @property int $leadId
 * @property string $eventName
 * @property array|null $data
 */
class WebEngageLeadRequestJob extends BaseJob implements JobInterface
{
    public int $leadId;
    public string $eventName;
    public ?array $data = null;

    /**
     * @param int $leadId
     * @param string $eventName
     * @param array|null $data
     * @param float|null $timeStart
     * @param array $config
     */
    public function __construct(int $leadId, string $eventName, ?array $data = null, ?float $timeStart = null, $config = [])
    {
        $this->leadId = $leadId;
        $this->eventName = $eventName;
        $this->data = $data;
        parent::__construct($timeStart, $config);
    }

    /**
     * @param $queue
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();

        try {
            if (!$lead = Lead::findOne($this->leadId)) {
                throw new \RuntimeException('Lead not found by ID (' . $this->leadId . ')');
            }

            if (empty($this->data)) {
                $this->data = (new LeadEventService($lead, $this->eventName))->getData();
            }

            $webEngageEventForm = new WebEngageEventForm();
            if (!$webEngageEventForm->load($this->data)) {
                throw new \RuntimeException('WebEngageEventForm not loaded');
            }
            $webEngageRequestService = new WebEngageRequestService();
            $webEngageRequestService->addEvent($webEngageEventForm);
        } catch (Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'WebEngageLeadRequestJob:throwable'
            );
        }
    }
}
