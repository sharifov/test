<?php

namespace modules\flight\src\useCases\reprotectionCreate\service;

use common\components\HybridService;
use modules\flight\models\FlightRequest;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\exception\CheckRestrictionException;
use Yii;

/**
 * Class OtaRequestReProtectionService
 *
 * @property FlightRequest $flightRequest
 * @property ProductQuote $reProtectionQuote
 * @property Cases $case
 */
class OtaRequestReProtectionService
{
    private FlightRequest $flightRequest;
    private ProductQuote $reProtectionQuote;
    private Cases $case;

    /**
     * @param FlightRequest $flightRequest
     * @param ProductQuote $reProtectionQuote
     * @param Cases $case
     */
    public function __construct(
        FlightRequest $flightRequest,
        ProductQuote $reProtectionQuote,
        Cases $case
    ) {
        $this->flightRequest = $flightRequest;
        $this->reProtectionQuote = $reProtectionQuote;
        $this->case = $case;
    }

    public function send(): ?array
    {
        $hybridService = Yii::createObject(HybridService::class);
        $data = [
            'data' => [
                'booking_id' => $this->flightRequest->fr_booking_id,
                'reprotection_quote_gid' => $this->reProtectionQuote->pq_gid,
                'case_gid' => $this->case->cs_gid,
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