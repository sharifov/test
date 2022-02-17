<?php

namespace src\model\leadPoorProcessing\service\rules;

use yii\helpers\ArrayHelper;

class LeadPoorProcessingSendSmsOffer extends AbstractLeadPoorProcessingService implements LeadPoorProcessingServiceInterface
{
    public function checkCondition(): bool
    {
        if (
            ($excludeProjects = $this->getRule()->lppd_params_json['excludeProjects'] ?? null) &&
            is_array($excludeProjects) &&
            $leadProjectKey = $this->getLead()->project->project_key
        ) {
            if (ArrayHelper::isIn($leadProjectKey, $excludeProjects)) {
                throw new \RuntimeException('Lead Project(' . $leadProjectKey . ') excluded from rule (' . $this->getRule()->lppd_key . ')');
            }
        }
        return parent::checkCondition();
    }

    public function handle(): void
    {
        $client = $this->getLead()->client;
        if ($client && empty($client->emailList) && !empty($client->clientPhones)) {
            parent::handle();
        }
    }
}
