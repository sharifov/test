<?php

namespace modules\product\src\entities\productQuoteChange;

class ProductQuoteChangeQuery
{
    public static function existsByQuoteIdAndStatuses(int $quoteId, array $statuses): bool
    {
        return ProductQuoteChange::find()->where(['pqc_pq_id' => $quoteId, 'pqc_status_id' => $statuses])->exists();
    }
}
