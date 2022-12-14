<?php

namespace modules\product\src\entities\productQuote;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ProductQuoteQuery
{
    public static function getOriginProductQuoteByAlternative(int $alternativeQuoteId): ?ProductQuote
    {
        $query = ProductQuote::find()
            ->innerJoin(
                ProductQuoteRelation::tableName(),
                new Expression(
                    'pq_id = pqr_parent_pq_id and pqr_related_pq_id = :quoteId and pqr_type_id = :typeId',
                    ['quoteId' => $alternativeQuoteId, 'typeId' => ProductQuoteRelation::TYPE_ALTERNATIVE]
                )
            );
        return $query->one();
    }

    public static function getOriginProductQuoteByReprotection(int $reprotectionQuoteId): ?ProductQuote
    {
        $query = ProductQuote::find()
            ->innerJoin(
                ProductQuoteRelation::tableName(),
                new Expression(
                    'pq_id = pqr_parent_pq_id and pqr_related_pq_id = :quoteId and pqr_type_id = :typeId',
                    ['quoteId' => $reprotectionQuoteId, 'typeId' => ProductQuoteRelation::TYPE_REPROTECTION]
                )
            );
        return $query->one();
    }

    public static function getOriginProductQuoteByChangeQuote(
        int $changeQuoteId,
        array $typeIds = [ProductQuoteRelation::TYPE_VOLUNTARY_EXCHANGE, ProductQuoteRelation::TYPE_REPROTECTION]
    ): ?ProductQuote {
        $query = ProductQuote::find()
            ->innerJoin(
                ProductQuoteRelation::tableName(),
                new Expression(
                    'pq_id = pqr_parent_pq_id and pqr_related_pq_id = :quoteId',
                    ['quoteId' => $changeQuoteId]
                )
            )
            ->andWhere(['IN', 'pqr_type_id', $typeIds]);
        return $query->one();
    }

    /**
     * @param int $offerId
     * @return ProductQuote[]
     */
    public static function getOriginQuotesRelatedOffer(int $offerId): array
    {
        $query = ProductQuote::find();
        $query->innerJoin(
            ProductQuoteRelation::tableName(),
            new Expression(
                'pq_id = pqr_parent_pq_id and pqr_type_id = :typeId',
                ['typeId' => ProductQuoteRelation::TYPE_ALTERNATIVE]
            )
        );
        $query->innerJoin(OfferProduct::tableName(), 'op_offer_id = :offerId and op_product_quote_id = pq_id', ['offerId' => $offerId]);
        return $query->all();
    }

    /**
     * @param int $offerId
     * @return ProductQuote[]
     */
    public static function getAlternativeQuotesRelatedOffer(int $offerId): array
    {
        $query = ProductQuote::find();
        $query->select(['product_quote.*']);
        $query->addSelect(['product_quote_relation.pqr_parent_pq_id']);
        $query->innerJoin(
            ProductQuoteRelation::tableName(),
            new Expression(
                'pq_id = pqr_related_pq_id and pqr_type_id = :typeId',
                ['typeId' => ProductQuoteRelation::TYPE_ALTERNATIVE]
            )
        );
        $query->innerJoin(OfferProduct::tableName(), 'op_offer_id = :offerId and op_product_quote_id = pq_id', ['offerId' => $offerId]);
        return $query->all();
    }

    public static function getProductQuoteByBookingId(string $bookingId): ?ProductQuote
    {
        if ($flightQuoteFlight = FlightQuoteFlight::find()->where(['fqf_booking_id' => $bookingId])->orderBy(['fqf_id' => SORT_DESC])->one()) {
            return ArrayHelper::getValue($flightQuoteFlight, 'fqfFq.fqProductQuote');
        }
        return null;
    }

    /**
     * @param int $id
     * @return ProductQuote[]
     */
    public static function getReprotectionQuotesByOriginQuote(int $id): array
    {
        return ProductQuote::find()
            ->with('productQuoteDataRecommended')
            ->innerJoin(ProductQuoteRelation::tableName(), 'pqr_related_pq_id = pq_id and pqr_parent_pq_id = :parentQuoteId and pqr_type_id = :typeId', [
                'typeId' => ProductQuoteRelation::TYPE_REPROTECTION,
                'parentQuoteId' => $id
            ])->all();
    }

    public static function getVoluntaryExchangeQuotesByOriginQuote(int $id): array
    {
        return ProductQuote::find()
            ->with('productQuoteDataRecommended')
            ->innerJoin(ProductQuoteRelation::tableName(), 'pqr_related_pq_id = pq_id and pqr_parent_pq_id = :parentQuoteId and pqr_type_id = :typeId', [
                'typeId' => ProductQuoteRelation::TYPE_VOLUNTARY_EXCHANGE,
                'parentQuoteId' => $id
            ])->all();
    }

    public static function getChangeQuotesByOriginQuote(
        int $id,
        array $typeIds = [ProductQuoteRelation::TYPE_VOLUNTARY_EXCHANGE, ProductQuoteRelation::TYPE_REPROTECTION]
    ): array {
        return ProductQuote::find()
            ->with('productQuoteDataRecommended')
            ->innerJoin(ProductQuoteRelation::tableName(), 'pqr_related_pq_id = pq_id and pqr_parent_pq_id = :parentQuoteId', [
                'parentQuoteId' => $id
            ])
            ->andWhere(['IN', 'pqr_type_id', $typeIds])
            ->all();
    }

    /**
     * @param int $quoteId
     * @param array $types
     * @param array $statuses
     * @return ProductQuote[]
     */
    public static function getRelatedQuoteByOriginTypesStatuses(int $quoteId, array $types, array $statuses): array
    {
        return ProductQuote::find()
            ->innerJoin(ProductQuoteRelation::tableName(), 'pqr_related_pq_id = pq_id and pqr_parent_pq_id = :parentQuoteId', [
                'parentQuoteId' => $quoteId,
            ])
            ->andWhere(['pqr_type_id' => $types])
            ->byStatuses($statuses)
            ->all();
    }

    /**
     * @param int $productQuoteChangeId
     * @return ProductQuote[]
     */
    public static function getProductQuotesByChangeQuote(int $productQuoteChangeId): array
    {
        return ProductQuote::find()
            ->andWhere([
                'pq_id' => ProductQuoteChangeRelation::find()
                    ->select(['pqcr_pq_id'])
                    ->byChangeId($productQuoteChangeId)
            ])->all();
    }
}
