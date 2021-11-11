<?php

namespace sales\model\leadUserConversion\service;

use common\models\Lead;
use common\models\LeadFlow;
use sales\model\leadUserConversion\entity\LeadUserConversion;
use sales\model\leadUserConversion\repository\LeadUserConversionRepository;

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

    public function add(
        int $leadId,
        int $userId,
        ?string $description = null,
        ?int $createdUserId = null,
        bool $validateUsers = true
    ): void {
        if ($validateUsers && $createdUserId !== null && $createdUserId !== $userId) {
            return;
        }

        $leadWasFollowUp = LeadFlow::find()
            ->andWhere(['lead_id' => $leadId])
            ->andWhere([
                'OR',
                ['status' => Lead::STATUS_FOLLOW_UP],
                ['lf_from_status_id' => Lead::STATUS_FOLLOW_UP],
            ])
            ->exists();
        if ($leadWasFollowUp) {
            return;
        }

        $leadUserConversion = LeadUserConversion::create(
            $leadId,
            $userId,
            $description,
            $createdUserId
        );
        $this->leadUserConversionRepository->save($leadUserConversion);
    }
}
