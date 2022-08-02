<?php

namespace modules\taskList\src\services\taskAssign\checker;

use common\models\Lead;
use common\models\query\ClientPhoneQuery;

class PhoneAssignChecker implements TaskAssignCheckerInterface
{
    private Lead $lead;

    public function __construct(
        Lead $lead
    ) {
        $this->lead = $lead;
    }

    public function check(): bool
    {
        if (empty($this->lead->client_id)) {
            return false;
        }
        return ClientPhoneQuery::getQueryClientPhoneByClientId($this->lead->client_id)->exists();
    }
}
