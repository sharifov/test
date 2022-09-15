<?php

namespace modules\flight\src\useCases\services\productQuote;

use DomainException;
use modules\flight\models\FlightQuoteFlight;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class ProductQuoteService
 */
class ProductQuoteService
{
    /**
     * @param string $bookingId
     * @return ProductQuote
     * @throws DomainException
     */
    public function getProductQuote(string $bookingId): ProductQuote
    {
        $flight = FlightQuoteFlight::find()->andWhere(['fqf_booking_id' => $bookingId])->orderBy(['fqf_id' => SORT_DESC])->one();
        if (!$flight) {
            throw new DomainException('Not found Flight Quote Flight. BookingId: ' . $bookingId);
        }
        $productQuote = $flight->fqfFq->fqProductQuote ?? null;
        if ($productQuote) {
            return $productQuote;
        }
        throw new DomainException('Not found Product Quote. BookingId: ' . $bookingId);
    }
}
