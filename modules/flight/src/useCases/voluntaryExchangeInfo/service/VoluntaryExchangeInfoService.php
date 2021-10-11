<?php

namespace modules\flight\src\useCases\voluntaryExchangeInfo\service;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;

/**
 * Class VoluntaryExchangeInfoService
 */
class VoluntaryExchangeInfoService
{
    public static function getLastProductQuoteChange(string $bookingId, int $duration = -1): ?ProductQuoteChange
    {
        return ProductQuoteChange::find()
            ->select(ProductQuoteChange::tableName() . '.*')
            ->innerJoin(FlightQuote::tableName(), 'fq_product_quote_id = pqc_pq_id')
            ->innerJoin(FlightQuoteFlight::tableName(), 'fqf_fq_id = fq_id')
            ->where(['fqf_booking_id' => $bookingId])
            ->andWhere(['pqc_type_id' => ProductQuoteChange::TYPE_VOLUNTARY_EXCHANGE])
            ->orderBy(['pqc_id' => SORT_DESC])
            ->cache($duration)
            ->one();
    }

    public static function apiDataMapper(ProductQuoteChange $productQuoteChange): array
    {
        return [
            'id' => 'pqc_id',
            'productQuoteId' => 'pqc_pq_id',
            'productQuoteGid' => static function (ProductQuoteChange $productQuoteChange) {
                return $productQuoteChange->pqcPq->pq_gid ?? null;
            },
            'caseId' => 'pqc_case_id',
            'caseGid' => static function (ProductQuoteChange $productQuoteChange) {
                return $productQuoteChange->pqcCase->cs_gid ?? null;
            },
            'statusId' => 'pqc_status_id',
            'statusName' => static function (ProductQuoteChange $productQuoteChange) {
                return ProductQuoteChangeStatus::getName($productQuoteChange->pqc_status_id);
            },
            'decisionTypeId' => 'pqc_decision_type_id',
            'decisionTypeName' => static function (ProductQuoteChange $productQuoteChange) {
                return ProductQuoteChangeDecisionType::getName($productQuoteChange->pqc_decision_type_id);
            },
            'isAutomate' => 'pqc_is_automate',
            'createdDt' => 'pqc_created_dt',
            'updatedDt' => 'pqc_updated_dt',
        ];
    }
}
