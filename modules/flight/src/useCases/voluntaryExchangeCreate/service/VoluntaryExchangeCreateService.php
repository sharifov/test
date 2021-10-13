<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\service;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use webapi\src\ApiCodeException;

/**
 * Class VoluntaryExchangeCreateService
 *
 * @property VoluntaryExchangeObjectCollection $objectCollection
 */
class VoluntaryExchangeCreateService
{
    public static function checkByPost(array $post): void
    {
        $hash = FlightRequest::generateHashFromDataJson($post);
        if ($flightRequest = FlightRequest::findOne(['fr_hash' => $hash])) {
            throw new \RuntimeException(
                'FlightRequest (hash: ' . $hash . ') already processed',
                ApiCodeException::REQUEST_ALREADY_PROCESSED
            );
        }
    }

    public static function checkByBookingId(
        string $bookingId,
        array $statuses = ProductQuoteChangeStatus::PROCESSING_LIST,
        int $typeId = ProductQuoteChange::TYPE_VOLUNTARY_EXCHANGE
    ): void {
        if ($productQuoteChange = self::getProductQuoteChangeByBookingId($bookingId, $statuses, $typeId)) {
            throw new \RuntimeException(
                'Product Quote Change already exist in status (' .
                    ProductQuoteChangeStatus::getName($productQuoteChange->pqc_status_id) . ')',
                ApiCodeException::REQUEST_ALREADY_PROCESSED
            );
        }
    }

    public static function getProductQuoteChangeByBookingId(
        string $bookingId,
        array $statuses = ProductQuoteChangeStatus::PROCESSING_LIST,
        int $typeId = ProductQuoteChange::TYPE_VOLUNTARY_EXCHANGE
    ): ?ProductQuoteChange {
        return ProductQuoteChange::find()
            ->select(ProductQuoteChange::tableName() . '.*')
            ->innerJoin(ProductQuote::tableName(), 'pq_id = pqc_pq_id')
            ->innerJoin(FlightQuote::tableName(), 'fq_product_quote_id = pq_id')
            ->innerJoin(FlightQuoteFlight::tableName(), 'fqf_fq_id = fq_id')
            ->where(['fqf_booking_id' => $bookingId])
            ->andWhere(['pqc_type_id' => $typeId])
            ->andWhere(['pqc_status_id' => $statuses])
            ->orderBy(['pqc_id' => SORT_DESC])
            ->one();
    }
}
