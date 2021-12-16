<?php

namespace modules\webEngage\src\service\webEngageEventData\lead\eventData;

use common\models\ApiUser;
use modules\webEngage\settings\WebEngageSettings;

/**
 * Class LeadCreatedEventData
 *
 */
class LeadCreatedEventData extends AbstractLeadEventData
{
    public static function checkByApiUser(?ApiUser $apiUser): bool
    {
        if (!$apiUser || empty($apiUser->au_api_username)) {
            return false;
        }
        if (!$names = (new WebEngageSettings())->leadCreatedApiUsernames()) {
            return false;
        }
        $names = explode(',', $names);
        return in_array($apiUser->au_api_username, $names, false);
    }
}
