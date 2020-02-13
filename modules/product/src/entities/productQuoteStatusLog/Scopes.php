<?php

namespace modules\product\src\entities\productQuoteStatusLog;

use yii\db\ActiveQuery;

/**
 * @see ProductQuoteStatusLog
 */
class Scopes extends ActiveQuery
{
    public function last(int $productQuoteId): self
    {
        return $this
            ->andWhere(['pqsl_product_quote_id' => $productQuoteId])
            ->orderBy(['pqsl_id' => SORT_DESC])
            ->limit(1);
    }
}
