<?php

namespace modules\product\src\entities\productQuoteLead;

/**
* @see ProductQuoteLead
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return ProductQuoteLead[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ProductQuoteLead|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byLeadId(int $id): Scopes
    {
        return $this->andWhere(['pql_lead_id' => $id]);
    }

    public function byQuoteId(int $id): Scopes
    {
        return $this->andWhere(['pql_product_quote_id' => $id]);
    }
}
