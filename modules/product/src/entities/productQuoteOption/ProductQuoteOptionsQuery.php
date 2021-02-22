<?php

namespace modules\product\src\entities\productQuoteOption;

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
}
