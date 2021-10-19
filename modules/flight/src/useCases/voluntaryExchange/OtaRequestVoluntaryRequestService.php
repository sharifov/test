<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use common\components\HybridService;
use modules\flight\models\FlightRequest;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\exception\CheckRestrictionException;
use Yii;

/**
 * Class OtaRequestVoluntaryRequestService
 *
 * @property FlightRequest $flightRequest
 * @property ProductQuote $voluntaryQuote
 * @property ProductQuote $originProductQuote
 * @property Cases $case
 */
class OtaRequestVoluntaryRequestService
{
    public static function success(
        FlightRequest $flightRequest,
        ProductQuote $voluntaryQuote,
        ProductQuote $originProductQuote,
        Cases $case
    ): ?array {
        $data = [
            'data' => [
                'booking_id' => $flightRequest->fr_booking_id,
                'voluntary_quote_gid' => $voluntaryQuote->pq_gid,
                'origin_quote_gid' => $originProductQuote->pq_gid,
                'case_gid' => $case->cs_gid,
            ]
        ];
        $hybridService = Yii::createObject(HybridService::class);
        if (!$result = $hybridService->whVoluntaryExchangeSuccess($flightRequest->fr_project_id, $data)) {
            throw new \RuntimeException('Not found webHookEndpoint in project (' . $flightRequest->fr_project_id . ')');
        }
        $case->addEventLog(
            CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
            'Request HybridService sent successfully'
        );
        return $result ?? null;
    }

    public static function fail(
        FlightRequest $flightRequest,
        ?ProductQuote $originProductQuote
    ): ?array {
        $data = [
            'data' => [
                'booking_id' => $flightRequest->fr_booking_id,
                'origin_quote_gid' => $originProductQuote->pq_gid ?? null,
            ]
        ];
        $hybridService = Yii::createObject(HybridService::class);
        if (!$result = $hybridService->whVoluntaryExchangeFail($flightRequest->fr_project_id, $data)) {
            throw new \RuntimeException('Not found webHookEndpoint in project (' . $flightRequest->fr_project_id . ')');
        }
        return $result ?? null;
    }
}
