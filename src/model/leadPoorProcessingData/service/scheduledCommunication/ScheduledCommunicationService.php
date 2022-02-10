<?php

namespace src\model\leadPoorProcessingData\service\scheduledCommunication;

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use yii\helpers\ArrayHelper;

/**
 * Class ScheduledCommunicationService
 */
class ScheduledCommunicationService
{
    private LeadPoorProcessingData $model;
    private int $intervalHour = 24;
    private int $smsOut = 1;
    private int $emailOffer = 1;
    private int $callOut = 2;

    public function __construct(LeadPoorProcessingData $model)
    {
        if ($model->lppd_key !== LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION) {
            throw new \RuntimeException('LeadPoorProcessingData not is ' . LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION);
        }
        $this->model = $model;
        $this->fillProperty();
    }

    private function fillProperty(): void
    {
        $this->intervalHour = (int) ArrayHelper::getValue($this->model, 'lppd_params_json.intervalHour', $this->intervalHour);
        $this->smsOut = (int) ArrayHelper::getValue($this->model, 'lppd_params_json.smsOut', $this->smsOut);
        $this->emailOffer = (int) ArrayHelper::getValue($this->model, 'lppd_params_json.emailOffer', $this->emailOffer);
        $this->callOut = (int) ArrayHelper::getValue($this->model, 'lppd_params_json.callOut', $this->callOut);
    }

    public function getIntervalHour(): int
    {
        return $this->intervalHour;
    }

    public function getSmsOut(): int
    {
        return $this->smsOut;
    }

    public function getEmailOffer(): int
    {
        return $this->emailOffer;
    }

    public function getCallOut(): int
    {
        return $this->callOut;
    }
}
