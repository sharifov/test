<?php

namespace src\model\leadUserRating\service;

use src\helpers\ErrorsToStringHelper;
use src\model\leadUserRating\entity\LeadUserRating;
use src\model\leadUserRating\entity\LeadUserRatingQuery;
use src\model\leadUserRating\repository\LeadUserRatingRepository;

class LeadUserRatingService
{
    /**
     * @param int $leadId
     * @param int $userId
     * @param int $rating
     * @return void
     */
    public static function createOrUpdate(
        int $leadId,
        int $userId,
        int $rating
    ): void {
        if (!$leadUserRating = LeadUserRatingQuery::getByLeadAndUserId($leadId, $userId)) {
            $leadUserRating = LeadUserRating::create($leadId, $userId, $rating);
        }

        if (!$leadUserRating->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($leadUserRating, ' '));
        }

        $leadUserRating->lur_rating = $rating;
        $leadPoorProcessingRepository = new LeadUserRatingRepository($leadUserRating);
        $leadPoorProcessingRepository->save();
    }
}
