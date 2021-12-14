<?php

namespace common\components\jobs;

use common\models\Lead;
use modules\webEngage\form\WebEngageEventForm;
use modules\webEngage\form\WebEngageUserForm;
use modules\webEngage\src\service\webEngageEventData\lead\LeadEventDictionary;
use modules\webEngage\src\service\webEngageEventData\lead\LeadEventService;
use modules\webEngage\src\service\WebEngageRequestService;
use modules\webEngage\src\service\webEngageUserData\WebEngageUserDataService;
use modules\webEngage\src\service\webEngageUserData\WebEngageUserService;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\model\clientData\service\ClientDataService;
use sales\model\clientDataKey\entity\ClientDataKeyDictionary;
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
    public function __construct(int $leadId, string $eventName, ?array $data = null, ?float $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;
        $this->eventName = $eventName;
        $this->data = $data;
        parent::__construct($timeStart, $config);
    }

    /**
     * @param $queue
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();

        try {
            if (!$lead = Lead::findOne($this->leadId)) {
                throw new \RuntimeException('Lead not found by ID (' . $this->leadId . ')');
            }
            $leadEventService = new LeadEventService($lead, $this->eventName);
            if (!$leadEventService->isSourceCIDChecked()) {
                $cid = $lead->source->cid ?? '';
                throw new \RuntimeException('SourceCIDChecked is failed. CID(' . $cid . ')', -1);
            }

            $webEngageUserService = new WebEngageUserService($this->eventName, $lead->client ?? null);

            if ($webEngageUserService->isSendUserCreateRequest()) {
                $webEngageUserForm = new WebEngageUserForm();
                if (!$webEngageUserForm->load((new WebEngageUserDataService($lead))->getData())) {
                    throw new \RuntimeException('WebEngageUserForm not loaded');
                }
                if (!$webEngageUserForm->validate()) {
                    throw new \RuntimeException('WebEngageUserForm : ' .
                        ErrorsToStringHelper::extractFromModel($webEngageUserForm, ' '));
                }
                (new WebEngageRequestService())->addUser($webEngageUserForm);
                ClientDataService::setValue(
                    $lead->client->id,
                    ClientDataKeyDictionary::IS_SEND_TO_WEB_ENGAGE,
                    '1'
                );
            }

            if (empty($this->data)) {
                $this->data = $leadEventService->getData();
            }
            $webEngageEventForm = new WebEngageEventForm();
            if (!$webEngageEventForm->load($this->data)) {
                throw new \RuntimeException('WebEngageEventForm not loaded');
            }
            if (!$webEngageEventForm->validate()) {
                throw new \RuntimeException('WebEngageEventForm : ' .
                    ErrorsToStringHelper::extractFromModel($webEngageEventForm, ' '));
            }
            (new WebEngageRequestService())->addEvent($webEngageEventForm);
        } catch (\RuntimeException $throwable) {
            if ($throwable->getCode() >= 0) {
                \Yii::warning(AppHelper::throwableLog($throwable),'WebEngageLeadRequestJob:warning');
            }
        } catch (Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'WebEngageLeadRequestJob:throwable'
            );
        }
    }
}
