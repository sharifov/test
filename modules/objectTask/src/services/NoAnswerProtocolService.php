<?php

namespace modules\objectTask\src\services;

use common\models\Lead;
use modules\objectTask\src\scenarios\NoAnswer;
use src\model\leadData\entity\LeadDataQuery;
use src\model\leadDataKey\services\LeadDataKeyDictionary;

class NoAnswerProtocolService
{
    public static function leadWasInNoAnswer(Lead $lead): bool
    {
        return (bool) LeadDataQuery::getOneByLeadKeyValue(
            $lead->id,
            LeadDataKeyDictionary::KEY_AUTO_FOLLOW_UP,
            NoAnswer::KEY
        );
    }
}
