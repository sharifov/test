<?php

namespace modules\flight\src\useCases\reprotectionDecision;

use common\components\BackOffice;
use modules\product\src\entities\productQuote\ProductQuote;
use src\helpers\app\AppHelper;
use webapi\src\request\RequestBoAdditionalSources;

class BoCustomerDecisionService implements CustomerDecisionService
{
    public function reprotectionCustomerDecisionConfirm(
        int $projectId,
        string $bookingId,
        array $quote,
        string $reprotectionQuoteGid
    ): bool {
        $additionalInfo = $this->mappingAdditionalInfo($reprotectionQuoteGid);
        return BackOffice::reprotectionCustomerDecisionConfirm($projectId, $bookingId, $quote, $reprotectionQuoteGid, $additionalInfo);
    }

    public function reprotectionCustomerDecisionModify(
        int $projectId,
        string $bookingId,
        array $quote,
        string $reprotectionQuoteGid
    ): bool {
        $additionalInfo = $this->mappingAdditionalInfo($reprotectionQuoteGid);
        return BackOffice::reprotectionCustomerDecisionModify($projectId, $bookingId, $quote, $reprotectionQuoteGid, $additionalInfo);
    }

    public function reprotectionCustomerDecisionRefund(int $projectId, string $bookingId): bool
    {
        return BackOffice::reprotectionCustomerDecisionRefund($projectId, $bookingId);
    }

    public function mappingAdditionalInfo($reprotectionQuoteGid): array
    {
        $additionalInfo = [];
        if ($reprotectionQuoteGid) {
            $productQuote = ProductQuote::findByGid($reprotectionQuoteGid);
            if ($productQuote) {
                try {
                    $service = RequestBoAdditionalSources::getServiceByType(RequestBoAdditionalSources::TYPE_PRODUCT_QUOTE);
                    if (!$service) {
                        throw new \RuntimeException('Service not found by type: ' . RequestBoAdditionalSources::getTypeNameById(RequestBoAdditionalSources::TYPE_PRODUCT_QUOTE));
                    }
                    $additionalInfo = $service->prepareAdditionalInfo($productQuote);
                } catch (\Throwable $e) {
                    \Yii::error(AppHelper::throwableLog($e, true), 'BoCustomerDecisionService:mappingAdditionalInfo:additionalInfo');
                }
            } else {
                \Yii::error([
                    'message' => 'Not found product quote by gid: ' . $reprotectionQuoteGid,
                ], 'BoCustomerDecisionService:mappingAdditionalInfo:additionalInfo');
            }
        }

        return $additionalInfo;
    }
}
