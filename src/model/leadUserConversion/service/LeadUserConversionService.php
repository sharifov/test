<?php

namespace src\model\leadUserConversion\service;

use common\models\Lead;
use common\models\LeadFlow;
use modules\featureFlag\FFlag;
use src\model\leadUserConversion\entity\LeadUserConversion;
use src\model\leadUserConversion\repository\LeadUserConversionRepository;
use Yii;

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
        if (self::leadIsExcludeFromConversionByDescription($leadId, $description) === true) {
            return false;
        }

        $leadUserConversion = LeadUserConversion::create($leadId, $userId, $description, $createdUserId);
        $this->leadUserConversionRepository->save($leadUserConversion);
        return true;
    }

    public function addAutomate(int $leadId, int $userId, ?string $description = null, ?int $createdUserId = null): bool
    {
        if (self::leadIsExcludeFromConversionByDescription($leadId, $description) === true) {
            return false;
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

    public static function leadIsExcludeFromConversionByDescription(int $leadId, string $description): bool
    {
        if (in_array($description, [LeadUserConversionDictionary::DESCRIPTION_MANUAL, LeadUserConversionDictionary::DESCRIPTION_TAKE]) === true) {
            /** @fflag FFlag::FF_KEY_EXCLUDE_TAKE_CREATE_FROM_LEAD_USER_CONVERSION_BY_SOURCE_ENABLED, Exclude add lead to LeadUserConversion table by source */
            if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_EXCLUDE_TAKE_CREATE_FROM_LEAD_USER_CONVERSION_BY_SOURCE_ENABLED)) {
                $sources = Yii::$app->params['settings']['exclude_take_create_from_lead_user_conversion_by_source'] ?? [];

                if (empty($sources)) {
                    return false;
                }

                $leadSource = Lead::findOne(['id' => $leadId])->source->cid;

                return in_array($leadSource, $sources);
            }
        }

        return false;
    }
}
