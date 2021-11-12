<?php

namespace modules\product\src\entities\productQuoteOption;

use modules\product\src\entities\productOption\ProductOption;
use yii\db\Query;

class ProductQuoteOptionsQuery
{
    public static function getTotalSumClientPriceByQuote(int $quoteId): array
    {
        $query = new Query();
        $query->select('SUM(pqo_client_price) as client_price')
            ->from(ProductQuoteOption::tableName())
            ->where(['pqo_product_quote_id' => $quoteId]);
        return $query->one();
    }

    public static function getTotalSumPriceByQuote(int $quoteId): array
    {
        $query = new Query();
        $query->select('SUM(pqo_price + pqo_extra_markup) as total_price')
            ->from(ProductQuoteOption::tableName())
            ->where(['pqo_product_quote_id' => $quoteId]);
        return $query->one();
    }

    public static function getByProductQuoteIdOptionKey(int $quoteId, string $key): ?ProductQuoteOption
    {
        return ProductQuoteOption::find()
            ->join('JOIN', ProductOption::tableName(), 'pqo_product_option_id = po_id and REGEXP_REPLACE(LOWER(po_key), \'[^a-zA-Z0-9]+\', \'\') = :productOptionKey', [
                'productOptionKey' => strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $key))
            ])
            ->where(['pqo_product_quote_id' => $quoteId])
            ->one();
    }
}
