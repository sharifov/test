<?php

namespace src\model\leadData\services;

use common\models\Lead;
use src\model\leadData\entity\LeadData;
use src\model\leadData\repository\LeadDataRepository;

/**
 * Class LeadDataCloneService
 */
class LeadDataCloneService
{
    public static function cloneByLead(Lead $oldLead, int $cloneLeadId): ?int
    {
        if (!$oldLead->leadData) {
            return null;
        }

        $clonedCount = 0;
        $leadDataRepository = new LeadDataRepository();
        foreach ($oldLead->leadData as $leadData) {
            $leadData = LeadData::create(
                $cloneLeadId,
                $leadData->ld_field_key,
                $leadData->ld_field_value
            );
            $leadDataRepository->save($leadData);
            $clonedCount++;
        }
        return $clonedCount;
    }
}
