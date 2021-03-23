<?php

namespace modules\offer\src\entities\offer;

use yii\db\ActiveQuery;

class OfferQuery extends ActiveQuery
{
    public static function existsOffersByLeadId(int $leadId): bool
    {
        return Offer::find()->where(['of_lead_id' => $leadId])->exists();
    }
}
