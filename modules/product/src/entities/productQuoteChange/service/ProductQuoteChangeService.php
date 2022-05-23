<?php

namespace modules\product\src\entities\productQuoteChange\service;

use common\components\hybrid\HybridWhData;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelationRepository;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use Yii;

/**
 * Class ProductQuoteChangeService
 */
class ProductQuoteChangeService
{
    public static function notRefundableReProtection(int $productQuoteId): bool
    {
        return ProductQuoteChange::find()
            ->where(['pqc_pq_id' => $productQuoteId])
            ->andWhere(['pqc_status_id' => SettingHelper::getInvoluntaryChangeActiveStatuses()])
            ->andWhere(['pqc_type_id' => ProductQuoteChange::TYPE_RE_PROTECTION])
            ->andWhere(['pqc_refund_allowed' => false])
            ->exists();
    }

    public static function lastActiveReProtection(int $productQuoteId): ?ProductQuoteChange
    {
        return ProductQuoteChange::find()
            ->where(['pqc_pq_id' => $productQuoteId])
//            ->andWhere(['pqc_status_id' => SettingHelper::getInvoluntaryChangeActiveStatuses()])
            ->andWhere(['pqc_type_id' => ProductQuoteChange::TYPE_RE_PROTECTION])
            ->one();
    }



    public static function sendHybridDeclineNotification(
        ProductQuoteChange $productQuoteChange,
        ProductQuote $changeQuote,
        Cases $case
    ) {
        $whData = [];
        $whType = null;
        try {
            if (!ProductQuoteChangeRelationRepository::hasAvailableProductQuotes($productQuoteChange->pqc_id, $changeQuote->pq_id)) {
                if ($productQuoteChange->isTypeReProtection()) {
                    $originQuote = ProductQuoteQuery::getOriginProductQuoteByChangeQuote($changeQuote->pq_id);
                    $whType = HybridWhData::WH_TYPE_FLIGHT_SCHEDULE_CHANGE;
                    $whData = (new HybridWhData())->fillCollectedData(
                        $whType,
                        [
                            'booking_id' => $case->cs_order_uid,
                            'reprotection_quote_gid' => $changeQuote->pq_gid,
                            'case_gid' => $case->cs_gid,
                            'product_quote_gid' => $originQuote->pq_gid ?? null,
                            'status' => ProductQuoteChangeStatus::getClientKeyStatusById(ProductQuoteChangeStatus::DECLINED),
                        ]
                    )->getCollectedData();
                } elseif ($productQuoteChange->isTypeVoluntary()) {
                    $whType = HybridWhData::WH_TYPE_VOLUNTARY_CHANGE_UPDATE;
                    $whData = (new HybridWhData())->fillCollectedData(
                        $whType,
                        [
                            'booking_id' => $case->cs_order_uid,
                            'product_quote_gid' => $changeQuote->pq_gid,
                            'exchange_gid' => $productQuoteChange->pqc_gid,
                            'exchange_status' => ProductQuoteChangeStatus::getClientKeyStatusById(ProductQuoteChangeStatus::DECLINED),
                        ]
                    )->getCollectedData();
                }
                if (!empty($whData)) {
                    \Yii::$app->hybrid->wh(
                        $case->cs_project_id,
                        $whType,
                        ['data' => $whData]
                    );
                }
            }
        } catch (\Throwable $throwable) {
            $errorData = AppHelper::throwableLog($throwable);
            $errorData['text'] = 'OTA site is not informed (sendHybridDeclineNotification)';
            $errorData['project_id'] = $case->cs_project_id;
            $errorData['case_id'] = $case->cs_id;
            Yii::warning($errorData, 'ProductQuoteChangeService:sendHybridDeclineNotification:Throwable');
        }
    }
}
