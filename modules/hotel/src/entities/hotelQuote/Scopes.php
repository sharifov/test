<?php

namespace modules\hotel\src\entities\hotelQuote;

/**
 * @see \modules\hotel\models\HotelQuote
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byProductQuote(int $productQuoteId): self
    {
        return $this->andWhere(['hq_product_quote_id' => $productQuoteId]);
    }
}
