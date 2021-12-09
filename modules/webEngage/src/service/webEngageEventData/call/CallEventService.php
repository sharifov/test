<?php

namespace modules\webEngage\src\service\webEngageEventData\call;

use common\models\Call;
use common\models\Lead;

/**
 * Class CallEventService
 */
class CallEventService
{
    public static function getLead(int $callId, int $clientId): ?Lead
    {
        return Lead::find()
            ->select(Lead::tableName() . '.*')
            ->innerJoin(Call::tableName(), Lead::tableName() . '.id = c_lead_id AND c_id = :call_id', ['call_id' => $callId])
            ->where(['client_id' => $clientId])
            ->one();
    }
}
