<?php

namespace src\model\leadPoorProcessing\service\rules;

class LeadPoorProcessingSendSmsOffer extends AbstractLeadPoorProcessingService implements LeadPoorProcessingServiceInterface
{
    public function handle(): void
    {
        $client = $this->getLead()->client;
        if ($client && empty($client->emailList) && !empty($client->clientPhones)) {
            parent::handle();
        }
    }
}
