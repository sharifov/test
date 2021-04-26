<?php

namespace modules\fileStorage\src\entity\fileProductQuote;

use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class FileProductQuoteQuery
 */
class FileProductQuoteQuery
{
    /**
     * @param int $orderId
     * @param int $categoryId
     * @return FileProductQuote[]|null
     */
    public static function getByOrderAndCategory(int $orderId, int $categoryId): ?array
    {
        $productQuoteIds = ProductQuote::find()->select(['pq_id'])->byOrderId($orderId)->booked()->column();

        return FileProductQuote::find()
            ->innerJoin(
                FileOrder::tableName(),
                'fo_pq_id = fpq_pq_id'
            )
            ->where(['IN', 'fpq_pq_id', $productQuoteIds])
            ->andWhere([
                'fo_category_id' => $categoryId,
                'fo_or_id' => $orderId
            ])
            ->with(['file'])
            ->all();
    }

    /**
     * @param int $productQuoteId
     * @param int $orderId
     * @param int $categoryId
     * @return FileProductQuote[]|null
     */
    public static function getByQuoteOrderAndCategory(int $productQuoteId, int $orderId, int $categoryId): array
    {
        return FileProductQuote::find()
            ->innerJoin(
                FileOrder::tableName(),
                'fo_pq_id = fpq_pq_id'
            )
            ->where(['fpq_pq_id', $productQuoteId])
            ->andWhere([
                'fo_category_id' => $categoryId,
                'fo_or_id' => $orderId
            ])
            ->with(['file'])
            ->all();
    }
}
