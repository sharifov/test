<?php

namespace modules\product\src\entities\productQuoteChange\service;

use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use src\helpers\setting\SettingHelper;

/**
 * Class ProductQuoteChangeService
 */
class ProductQuoteChangeService
{
    public static function notRefundableReProtection(int $productQuoteId): bool
    {
        return ProductQuoteChange::find()
            ->where(['pqc_pq_id' => $productQuoteId])
            ->andWhere(['pqc_status_id' => SettingHelper::getInvoluntaryChangeActiveStatuses()])
            ->andWhere(['pqc_type_id' => ProductQuoteChange::TYPE_RE_PROTECTION])
            ->andWhere(['pqc_refund_allowed' => false])
            ->exists();
    }

    public static function lastActiveReProtection(int $productQuoteId): ?ProductQuoteChange
    {
        return ProductQuoteChange::find()
            ->where(['pqc_pq_id' => $productQuoteId])
//            ->andWhere(['pqc_status_id' => SettingHelper::getInvoluntaryChangeActiveStatuses()])
            ->andWhere(['pqc_type_id' => ProductQuoteChange::TYPE_RE_PROTECTION])
            ->one();
    }
}
