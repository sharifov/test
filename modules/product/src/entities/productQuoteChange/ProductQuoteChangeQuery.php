<?php

namespace modules\product\src\entities\productQuoteChange;

use sales\helpers\setting\SettingHelper;

class ProductQuoteChangeQuery
{
    public static function existsByQuoteIdAndStatuses(int $quoteId, array $statuses): bool
    {
        return ProductQuoteChange::find()->where(['pqc_pq_id' => $quoteId, 'pqc_status_id' => $statuses])->exists();
    }

    /**
     * @param int $quoteId
     * @return ProductQuoteChange[]
     */
    public static function findAllNotFinishedByProductQuoteId(int $quoteId): array
    {
        return ProductQuoteChange::find()->byProductQuote($quoteId)->excludeStatuses(SettingHelper::getFinishedQuoteChangeStatuses())->all();
    }
}
