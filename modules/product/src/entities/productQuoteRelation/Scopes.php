<?php

namespace modules\product\src\entities\productQuoteRelation;

/**
* @see ProductQuoteRelation
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return ProductQuoteRelation[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ProductQuoteRelation|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byRelatedQuoteId(int $id): Scopes
    {
        return $this->andWhere(['pqr_related_pq_id' => $id]);
    }

    public function alternative(): Scopes
    {
        return $this->andWhere(['pqr_type_id' => ProductQuoteRelation::TYPE_ALTERNATIVE]);
    }
}
