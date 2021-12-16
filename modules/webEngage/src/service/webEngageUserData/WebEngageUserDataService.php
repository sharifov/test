<?php

namespace modules\webEngage\src\service\webEngageUserData;

use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use modules\webEngage\settings\WebEngageSettings;

/**
 * Class WebEngageUserDataService
 *
 * @property Lead $lead
 * @property WebEngageSettings $settings
 */
class WebEngageUserDataService
{
    private Lead $lead;
    private WebEngageSettings $settings;

    /**
     * @param Lead $lead
          */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        $this->settings = new WebEngageSettings();
    }

    public function getData(): array
    {
        return [
            'userId' => $this->lead->client->uuid ?? null,
            'firstName' => $this->lead->l_client_first_name,
            'lastName' => $this->lead->l_client_last_name,
            'email' => $this->lead->l_client_email,
            'phone' => $this->lead->l_client_phone,
            'attributes' => [
                'clientFirstName' => $this->lead->client->first_name ?? null,
                'clientLastName' => $this->lead->client->last_name ?? null,
                'clientPhone' => ClientPhone::getGeneralPhone((int) $this->lead->client_id),
                'clientEmail' => ClientEmail::getGeneralEmail((int) $this->lead->client_id),
                'isTest' => $this->settings->isTest(),
            ],
        ];
    }
}
