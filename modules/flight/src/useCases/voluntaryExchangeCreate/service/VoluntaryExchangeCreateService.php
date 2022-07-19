<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\service;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
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
        array $statuses,
        array $notQuoteStatuses,
        array $typeIds
    ): void {
        if ($productQuoteCheck = self::getProductQuoteChangeByBookingId($bookingId, $statuses, $notQuoteStatuses, $typeIds)) {
            throw new \RuntimeException(
                'Quote not available for exchange.
                    Product Quote status (' . ProductQuoteStatus::getName($productQuoteCheck['pq_status_id']) . ') 
                    Product Quote Change status (' . ProductQuoteChangeStatus::getName($productQuoteCheck['pqc_status_id']) . ')',
                ApiCodeException::REQUEST_ALREADY_PROCESSED
            );
        }
    }

    public static function getProductQuoteChangeByBookingId(
        string $bookingId,
        array $changeStatuses,
        array $notQuoteStatuses,
        array $typeIds
    ): ?array {
        return ProductQuoteChange::find()
            ->select(ProductQuoteChange::tableName() . '.pqc_status_id')
            ->addSelect(ProductQuote::tableName() . '.pq_status_id')
            ->innerJoin(ProductQuote::tableName(), 'pq_id = pqc_pq_id')
            ->innerJoin(FlightQuote::tableName(), 'fq_product_quote_id = pq_id')
            ->innerJoin(FlightQuoteFlight::tableName(), 'fqf_fq_id = fq_id')
            ->where(['fqf_booking_id' => $bookingId])
            ->andWhere(['IN', 'pqc_type_id', $typeIds])
            ->andWhere(['pqc_status_id' => $changeStatuses])
            ->andWhere(['NOT IN', 'pq_status_id', $notQuoteStatuses])
            ->orderBy(['pqc_id' => SORT_DESC])
            ->asArray()
            ->one();
    }

    public static function getLastProductQuoteChangeByPqId(
        string $productQuoteId,
        ?array $statuses = null,
        array $typeIds = [ProductQuoteChange::TYPE_VOLUNTARY_EXCHANGE]
    ): ?ProductQuoteChange {
        $query = ProductQuoteChange::find()
            ->where(['pqc_pq_id' => $productQuoteId])
            ->andWhere(['IN', 'pqc_type_id', $typeIds])
            ->orderBy(['pqc_id' => SORT_DESC]);

        if ($statuses) {
            $query->andWhere(['IN', 'pqc_status_id', $statuses]);
        }

        return $query->one();
    }

    public static function getOriginProductQuote(
        string $bookingId
    ): ?ProductQuote {
        return ProductQuote::find()
            ->select(ProductQuote::tableName() . '.*')
            ->innerJoin(FlightQuote::tableName(), 'fq_product_quote_id = pq_id')
            ->innerJoin(FlightQuoteFlight::tableName(), 'fqf_fq_id = fq_id')
            ->where(['fqf_booking_id' => $bookingId])
            ->orderBy(['pq_id' => SORT_DESC])
            ->one();
    }

    public static function getOriginProductQuoteById(
        int $productQuoteId
    ): ?ProductQuote {
        return ProductQuote::find()
            ->where(['pq_id' => $productQuoteId])
            ->one();
    }

    public static function getVoluntaryProductQuote(
        string $gid
    ): ?ProductQuote {
        return ProductQuote::find()
            ->where(['pq_gid' => $gid])
            ->one();
    }

    public static function getProductQuoteByProductQuoteChange(
        int $productQuoteChangeId,
        ?array $statuses = null
    ): ?ProductQuote {
        $query = ProductQuote::find()
            ->select(ProductQuote::tableName() . '.*')
            ->innerJoin(ProductQuoteChangeRelation::tableName(), 'pq_id = pqcr_pq_id')
            ->where(['pqcr_pqc_id' => $productQuoteChangeId])
            ->orderBy(['pq_id' => SORT_DESC]);

        if ($statuses) {
            $query->andWhere(['IN', 'pq_status_id', $statuses]);
        }

        return $query->one();
    }

    public static function getProductQuoteChangeByProductQuote(
        int $productQuoteId,
        ?array $statuses = null
    ): ?ProductQuoteChange {
        $query = ProductQuoteChange::find()
            ->select(ProductQuoteChange::tableName() . '.*')
            ->innerJoin(ProductQuoteChangeRelation::tableName(), 'pqc_id = pqcr_pqc_id')
            ->where(['pqcr_pq_id' => $productQuoteId])
            ->orderBy(['pqc_id' => SORT_DESC]);

        if ($statuses) {
            $query->andWhere(['IN', 'pqc_status_id', $statuses]);
        }

        return $query->one();
    }
}
