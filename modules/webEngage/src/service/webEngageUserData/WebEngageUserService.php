<?php

namespace modules\webEngage\src\service\webEngageUserData;

use common\models\Client;
use modules\webEngage\settings\WebEngageSettings;
use sales\model\clientData\service\ClientDataService;
use sales\model\clientDataKey\entity\ClientDataKeyDictionary;

/**
 * Class WebEngageUserService
 *
 * @property string $eventName
 * @property Client|null $client
 * @property WebEngageSettings $settings
 */
class WebEngageUserService
{
    private string $eventName;
    private ?Client $client;
    private WebEngageSettings $settings;

    /**
     * @param string $eventName
     */
    public function __construct(string $eventName, ?Client $client)
    {
        $this->eventName = $eventName;
        $this->client = $client;
        $this->settings = new WebEngageSettings();
    }

    public function isSendUserCreateRequest(): bool
    {
        if (!$this->settings->isEnabled()) {
            return false;
        }
        if (!$this->settings->isSendUserCreateRequest($this->eventName)) {
            return false;
        }
        if (!$this->client) {
            return false;
        }

        return !ClientDataService::existByClientKeyIdValue(
            $this->client->id,
            ClientDataKeyDictionary::IS_SEND_TO_WEB_ENGAGE,
            '1'
        );
    }
}
