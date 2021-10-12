<?php

namespace modules\product\src\entities\productQuoteRelation;

use modules\product\src\entities\productQuoteData\ProductQuoteData;
use modules\product\src\entities\productQuoteData\ProductQuoteDataKey;

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

    public function reprotection(): Scopes
    {
        return $this->andWhere(['pqr_type_id' => ProductQuoteRelation::TYPE_REPROTECTION]);
    }

    public function voluntaryExchange(): Scopes
    {
        return $this->andWhere(['pqr_type_id' => ProductQuoteRelation::TYPE_VOLUNTARY_EXCHANGE]);
    }

    public function byParentQuoteId(int $id): Scopes
    {
        return $this->andWhere(['pqr_parent_pq_id' => $id]);
    }

    public function leftJoinRecommended(): Scopes
    {
        return $this->leftJoin(ProductQuoteData::tableName(), 'pqd_product_quote_id = pqr_related_pq_id and pqd_key = :key', [
            'key' => ProductQuoteDataKey::RECOMMENDED
        ]);
    }

    public function orderByRecommendedDesc()
    {
        return $this->orderBy([
            'pqd_value' => SORT_DESC
        ]);
    }
}
