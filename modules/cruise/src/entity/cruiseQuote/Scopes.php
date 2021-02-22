<?php

namespace modules\cruise\src\entity\cruiseQuote;

/**
* @see CruiseQuote
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function byProductQuote(int $productQuoteId): self
    {
        return $this->andWhere(['crq_product_quote_id' => $productQuoteId]);
    }

    /**
    * @return CruiseQuote[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return CruiseQuote|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
