<?php

namespace modules\flight\src\useCases\reprotectionCreate\service;

use common\components\HybridService;
use modules\flight\models\FlightRequest;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\exception\CheckRestrictionException;
use Yii;

/**
 * Class OtaRequestReProtectionService
 *
 * @property FlightRequest $flightRequest
 * @property ProductQuote $reProtectionQuote
 * @property ProductQuote $originProductQuote
 * @property Cases $case
 */
class OtaRequestReProtectionService
{
    private FlightRequest $flightRequest;
    private ProductQuote $reProtectionQuote;
    private ProductQuote $originProductQuote;
    private Cases $case;

    /**
     * @param FlightRequest $flightRequest
     * @param ProductQuote $reProtectionQuote
     * @param ProductQuote $originProductQuote
     * @param Cases $case
     */
    public function __construct(
        FlightRequest $flightRequest,
        ProductQuote $reProtectionQuote,
        ProductQuote $originProductQuote,
        Cases $case
    ) {
        $this->flightRequest = $flightRequest;
        $this->reProtectionQuote = $reProtectionQuote;
        $this->originProductQuote = $originProductQuote;
        $this->case = $case;
    }

    public function send(): ?array
    {
        $hybridService = Yii::createObject(HybridService::class);
        if (!$productQuoteChange = $this->reProtectionQuote->productQuoteChangeLastRelation->pqcrPqc ?? null) {
            throw new \RuntimeException('productQuoteChange not found');
        }
        $data = [
            'data' => [
                'booking_id' => $this->flightRequest->fr_booking_id,
                'reprotection_quote_gid' => $this->reProtectionQuote->pq_gid,
                'case_gid' => $this->case->cs_gid,
                'product_quote_gid' => $this->originProductQuote->pq_gid,
                'status' => ProductQuoteChangeStatus::getClientKeyStatusById($productQuoteChange->pqc_status_id),
            ]
        ];
        if (!$result = $hybridService->whReprotection($this->flightRequest->fr_project_id, $data)) {
            throw new CheckRestrictionException(
                'Not found webHookEndpoint in project (' . $this->flightRequest->fr_project_id . ')'
            );
        }
        $this->case->addEventLog(CaseEventLog::RE_PROTECTION_CREATE, 'Request HybridService sent successfully');

        return $result ?? null;
    }
}
