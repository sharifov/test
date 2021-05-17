<?php

namespace modules\offer\src\entities\offer;

use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use yii\db\ActiveQuery;

class OfferQuery extends ActiveQuery
{
    public static function existsOffersByLeadId(int $leadId): bool
    {
        return Offer::find()->where(['of_lead_id' => $leadId])->exists();
    }

    public static function getRelatedAlternativeProductQuotes(int $offerId)
    {
        $query = Offer::find();

        $query->innerJoin(OfferProduct::tableName(), 'of_id = op_offer_id');
        $query->innerJoin(ProductQuoteRelation::tableName(), 'op_product_quote_id = pqr_related_pq_id and pqr_type_id = :type', [
            'type' => ProductQuoteRelation::TYPE_ALTERNATIVE
        ]);

        return $query->all();
    }
}
