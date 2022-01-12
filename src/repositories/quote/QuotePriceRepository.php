<?php

namespace src\repositories\quote;

use common\models\QuotePrice;

/**
 * Class QuotePriceRepository
 */
class QuotePriceRepository
{
    /**
     * @param QuotePrice $quotePrice
     * @return int
     */
    public function save(QuotePrice $quotePrice): int
    {
        if (!$quotePrice->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $quotePrice->id;
    }

    public static function findByQuoteIdAndPaxCode(int $quoteId, string $paxCode): ?QuotePrice
    {
        return QuotePrice::find()
            ->where(['quote_id' => $quoteId])
            ->andWhere(['passenger_type' => $paxCode])
            ->one();
    }
}
