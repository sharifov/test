<?php

namespace modules\product\src\entities\productQuoteChangeRelation;

/**
* @see ProductQuoteChangeRelation
*/
class ProductQuoteChangeRelationQueryScopes extends \yii\db\ActiveQuery
{
    public function byChangeId(int $productQuoteChangeId): self
    {
        return $this->andWhere(['pqcr_pqc_id' => $productQuoteChangeId]);
    }
}
