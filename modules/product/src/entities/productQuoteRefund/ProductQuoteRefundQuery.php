<?php

namespace modules\product\src\entities\productQuoteRefund;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;

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
}
