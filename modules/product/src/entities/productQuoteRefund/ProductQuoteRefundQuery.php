<?php

namespace modules\product\src\entities\productQuoteRefund;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use src\helpers\setting\SettingHelper;

class ProductQuoteRefundQuery
{
    public static function getByBookingId(string $bookingId, int $cacheDuration = -1): ?ProductQuoteRefund
    {
        return ProductQuoteRefund::find()
            ->innerJoin(FlightQuote::tableName(), 'fq_product_quote_id = pqr_product_quote_id')
            ->innerJoin(FlightQuoteFlight::tableName(), 'fqf_fq_id = fq_id')
            ->where(['fqf_booking_id' => $bookingId])
            ->orderBy(['pqr_id' => SORT_DESC])
            ->cache($cacheDuration)
            ->one();
    }

    public static function getByBookingIdAndCid(string $bookingId, string $cid, int $cacheDuration = -1): ?ProductQuoteRefund
    {
        return ProductQuoteRefund::find()
            ->innerJoin(FlightQuote::tableName(), 'fq_product_quote_id = pqr_product_quote_id')
            ->innerJoin(FlightQuoteFlight::tableName(), 'fqf_fq_id = fq_id')
            ->where(['fqf_booking_id' => $bookingId])
            ->andWhere(['pqr_cid' => $cid])
            ->orderBy(['pqr_id' => SORT_DESC])
            ->cache($cacheDuration)
            ->one();
    }

    public static function getByBookingIdGidStatuses(string $bookingId, string $gid, array $statuses)
    {
        return ProductQuoteRefund::find()
            ->innerJoin(FlightQuote::tableName(), 'fq_product_quote_id = pqr_product_quote_id')
            ->innerJoin(FlightQuoteFlight::tableName(), 'fqf_fq_id = fq_id')
            ->where(['fqf_booking_id' => $bookingId])
            ->byGid($gid)
            ->byStatuses($statuses)
            ->orderBy(['pqr_id' => SORT_DESC])
            ->one();
    }

    /**
     * @param int $productQuoteId
     * @return ProductQuoteRefund[]
     */
    public static function findAllNotFinishedByProductQuoteId(int $productQuoteId): array
    {
        return ProductQuoteRefund::find()->byProductQuoteId($productQuoteId)->excludeStatuses(SettingHelper::getFinishedQuoteRefundStatuses())->all();
    }
}
