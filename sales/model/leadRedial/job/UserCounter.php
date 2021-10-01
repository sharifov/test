<?php

namespace sales\model\leadRedial\job;

use sales\helpers\setting\SettingHelper;
use sales\model\leadRedial\entity\CallRedialUserAccess;

class UserCounter
{
    public function getCount(int $leadId): int
    {
        $agentsHasAccessToCall = CallRedialUserAccess::find()
            ->select('count(crua_lead_id)')
            ->andWhere('time_to_sec(TIMEDIFF(now(), crua_created_dt)) < :limitTime', [
                'limitTime' => SettingHelper::getRedialUserAccessExpiredSecondsLimit()
            ])
            ->andWhere(['crua_lead_id' => $leadId])
            ->scalar();

        return SettingHelper::getRedialGetLimitAgents() - (int)$agentsHasAccessToCall;
    }
}
