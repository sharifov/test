<?php

namespace modules\smartLeadDistribution\src\listeners;

use common\models\Lead;
use modules\featureFlag\FFlag;
use modules\smartLeadDistribution\src\services\SmartLeadDistributionService;
use src\events\lead\LeadCreatedEvent;
use src\model\leadData\entity\LeadData;
use src\model\leadData\repository\LeadDataRepository;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use src\repositories\lead\LeadBadgesRepository;
use Yii;

class LeadRatingCalculationListener
{
    /**
     * @param LeadCreatedEvent $event
     */
    public function handle(LeadCreatedEvent $event): void
    {
        /** @fflag FFlag::FF_KEY_SMART_LEAD_DISTRIBUTION_ENABLE, Smart Lead Distribution Enable */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SMART_LEAD_DISTRIBUTION_ENABLE) === false) {
            return;
        }

        $leadBadgeRepository = new LeadBadgesRepository();
        $lead = $leadBadgeRepository->getBusinessInboxQuery()
            ->andWhere(['id' => $event->lead->id])
            ->limit(1)
            ->one();

        if ($lead === null) {
            return;
        }

        $points = SmartLeadDistributionService::countPoints($event->lead);
        $category = SmartLeadDistributionService::getCategoryByPoints($points);

        try {
            $leadDataRepository = \Yii::createObject(LeadDataRepository::class);
            $leadDataPoints = LeadData::create($event->lead->id, LeadDataKeyDictionary::KEY_LEAD_RATING_POINTS, $points);
            $leadDataCategory = LeadData::create($event->lead->id, LeadDataKeyDictionary::KEY_LEAD_RATING_CATEGORY, $category);

            $leadDataRepository->save($leadDataPoints);
            $leadDataRepository->save($leadDataCategory);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'leadId' => $event->lead->id,
                'points' => $points,
                'category' => $category,
            ], 'LeadRatingCalculationListener::createDataKey');
        }
    }
}
