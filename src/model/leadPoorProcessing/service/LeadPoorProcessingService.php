<?php

namespace src\model\leadPoorProcessing\service;

use src\helpers\ErrorsToStringHelper;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessing\entity\LeadPoorProcessingQuery;
use src\model\leadPoorProcessing\repository\LeadPoorProcessingRepository;

/**
 * Class LeadPoorProcessingService
 */
class LeadPoorProcessingService
{
    public static function fundOrCreate(
        int $leadId,
        int $dataId,
        string $expirationDt
    ): LeadPoorProcessing {
        if (!$leadPoorProcessing = LeadPoorProcessingQuery::getByLeadAndKey($leadId, $dataId)) {
            $leadPoorProcessing = LeadPoorProcessing::create($leadId, $dataId, $expirationDt);
        }
        $leadPoorProcessing->lpp_expiration_dt = $expirationDt;

        if (!$leadPoorProcessing->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($leadPoorProcessing, ' '));
        }

        $leadPoorProcessingRepository = new LeadPoorProcessingRepository($leadPoorProcessing);
        $leadPoorProcessingRepository->save();
        return $leadPoorProcessingRepository->getModel();
    }
}
