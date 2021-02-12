<?php

namespace modules\flight\src\entities\flightQuote;

/**
 * @see \modules\flight\models\FlightQuote
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byProductQuote(int $productQuoteId): self
    {
        return $this->andWhere(['fq_product_quote_id' => $productQuoteId]);
    }
}
