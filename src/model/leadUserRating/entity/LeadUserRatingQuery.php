<?php

namespace src\model\leadUserRating\entity;

use yii\helpers\ArrayHelper;

/**
* @see LeadUserRating
*/
class LeadUserRatingQuery
{
    public static function getByLeadAndUserId(int $leadId, int $userId)
    {
        return LeadUserRating::find()
            ->where(['lur_lead_id' => $leadId])
            ->andWhere(['lur_user_id' => $userId])
            ->limit(1)
            ->one();
    }
    public static function getLeadIdsByUserAndRating(int $userId, int $rating)
    {
        return LeadUserRating
            ::find()
            ->select('lur_lead_id')
            ->where([
                'lur_user_id' => $userId,
            ])
            ->andWhere([
                'lur_rating' => $rating,
            ])
            ->column();
    }
}
