<?php

namespace modules\offer\src\entities\offerStatusLog;

use yii\db\ActiveQuery;

/**
 * @see OfferStatusLog
 */
class Scopes extends ActiveQuery
{
    public function last(int $offerId): self
    {
        return $this
            ->andWhere(['osl_offer_id' => $offerId])
            ->orderBy(['osl_id' => SORT_DESC])
            ->limit(1);
    }
}
