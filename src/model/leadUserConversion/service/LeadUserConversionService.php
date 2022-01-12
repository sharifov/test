<?php

namespace src\model\leadUserConversion\service;

use common\models\Lead;
use common\models\LeadFlow;
use src\model\leadUserConversion\entity\LeadUserConversion;
use src\model\leadUserConversion\repository\LeadUserConversionRepository;

/**
 * Class LeadUserConversionService
 *
 * @property LeadUserConversionRepository $leadUserConversionRepository
 */
class LeadUserConversionService
{
    private LeadUserConversionRepository $leadUserConversionRepository;

    public function __construct(LeadUserConversionRepository $leadUserConversionRepository)
    {
        $this->leadUserConversionRepository = $leadUserConversionRepository;
    }

    public static function getUserIdsByLead(int $leadId): array
    {
        return LeadUserConversion::find()
            ->select(['luc_user_id'])
            ->where(['luc_lead_id' => $leadId])
            ->indexBy('luc_user_id')
            ->column();
    }

    public function addManual(int $leadId, int $userId, ?string $description = null, ?int $createdUserId = null): bool
    {
        $leadUserConversion = LeadUserConversion::create($leadId, $userId, $description, $createdUserId);
        $this->leadUserConversionRepository->save($leadUserConversion);
        return true;
    }

    public function addAutomate(int $leadId, int $userId, ?string $description = null, ?int $createdUserId = null): bool
    {
        $leadWasFollowUp = LeadFlow::find()
            ->andWhere(['lead_id' => $leadId])
            ->andWhere([
                'OR',
                ['status' => Lead::STATUS_FOLLOW_UP],
                ['lf_from_status_id' => Lead::STATUS_FOLLOW_UP],
            ])
            ->exists();
        if ($leadWasFollowUp) {
            return false;
        }

        $leadUserConversion = LeadUserConversion::create(
            $leadId,
            $userId,
            $description,
            $createdUserId
        );
        $this->leadUserConversionRepository->save($leadUserConversion);

        return true;
    }
}
