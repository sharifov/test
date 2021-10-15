<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use modules\flight\models\FlightRequest;
use modules\flight\models\FlightRequestLog;
use yii\helpers\ArrayHelper;

/**
 * Class FlightRequestService
 *
 * @property FlightRequest $flightRequest
 * @property VoluntaryExchangeObjectCollection $objectCollection
 */
class FlightRequestService
{
    private FlightRequest $flightRequest;
    private VoluntaryExchangeObjectCollection $objectCollection;

    /**
     * @param FlightRequest $flightRequest
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     */
    public function __construct(
        FlightRequest $flightRequest,
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
    ) {
        $this->flightRequest = $flightRequest;
        $this->objectCollection = $voluntaryExchangeObjectCollection;
    }

    public function changeStatus(int $newStatus, string $description): FlightRequest
    {
        $oldStatus = $this->flightRequest->fr_status_id;
        if ($newStatus === FlightRequest::STATUS_PENDING) {
            $this->flightRequest->statusToPending();
        } elseif ($newStatus === FlightRequest::STATUS_ERROR) {
            $this->flightRequest->statusToError();
        } elseif ($newStatus === FlightRequest::STATUS_DONE) {
            $this->flightRequest->statusToDone();
        } else {
            $this->flightRequest->fr_status_id = $newStatus;
        }
        $this->objectCollection->getFlightRequestRepository()->save($this->flightRequest);

        $flightRequestLog = FlightRequestLog::create(
            $this->flightRequest->fr_id,
            $oldStatus,
            $this->flightRequest->fr_status_id,
            $description
        );
        $this->objectCollection->getFlightRequestLogRepository()->save($flightRequestLog);
        return $this->flightRequest;
    }

    public function pending(string $description): FlightRequest
    {
        return $this->changeStatus(FlightRequest::STATUS_PENDING, $description);
    }

    public function done(string $description): FlightRequest
    {
        return $this->changeStatus(FlightRequest::STATUS_DONE, $description);
    }

    public function error(string $description): FlightRequest
    {
        return $this->changeStatus(FlightRequest::STATUS_ERROR, $description);
    }

    public function isAutomate(): bool
    {
        return (bool) ArrayHelper::getValue($this->flightRequest, 'fr_data_json.is_automate', false);
    }
}
