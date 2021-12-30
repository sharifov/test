<?php

namespace modules\flight\src\useCases\reprotectionCreate\service;

use DomainException;
use modules\flight\models\FlightRequest;
use modules\flight\models\FlightRequestLog;
use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\repositories\flightRequestLog\FlightRequestLogRepository;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class FlightRequestService
 *
 * @property FlightRequestRepository $flightRequestRepository
 * @property FlightRequestLogRepository $flightRequestLogRepository
 * @property FlightRequest $flightRequest
 */
class FlightRequestService
{
    private FlightRequestRepository $flightRequestRepository;
    private FlightRequestLogRepository $flightRequestLogRepository;

    private ?FlightRequest $flightRequest = null;

    /**
     * @param FlightRequestRepository $flightRequestRepository
     * @param FlightRequestLogRepository $flightRequestLogRepository
     */
    public function __construct(
        FlightRequestRepository $flightRequestRepository,
        FlightRequestLogRepository $flightRequestLogRepository
    ) {
        $this->flightRequestRepository = $flightRequestRepository;
        $this->flightRequestLogRepository = $flightRequestLogRepository;
    }

    public function changeStatus(int $newStatus, string $description): FlightRequest
    {
        $oldStatus = $this->getFlightRequest()->fr_status_id;
        if ($newStatus === FlightRequest::STATUS_PENDING) {
            $this->getFlightRequest()->statusToPending();
        } elseif ($newStatus === FlightRequest::STATUS_ERROR) {
            $this->getFlightRequest()->statusToError();
        } elseif ($newStatus === FlightRequest::STATUS_DONE) {
            $this->getFlightRequest()->statusToDone();
        } else {
            $this->getFlightRequest()->fr_status_id = $newStatus;
        }
        $this->flightRequestRepository->save($this->getFlightRequest());

        $flightRequestLog = FlightRequestLog::create(
            $this->getFlightRequest()->fr_id,
            $oldStatus,
            $this->getFlightRequest()->fr_status_id,
            $description
        );
        $this->flightRequestLogRepository->save($flightRequestLog);
        return $this->getFlightRequest();
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

    public function getFlightRequest(): FlightRequest
    {
        if (!$this->flightRequest) {
            throw new DomainException('FlightRequest is empty');
        }
        return $this->flightRequest;
    }

    public function setFlightRequest(FlightRequest $flightRequest): void
    {
        $this->flightRequest = $flightRequest;
    }

    public function getIsRefundAllowed(): bool
    {
        return (bool) ArrayHelper::getValue($this->flightRequest, 'fr_data_json.refundAllowed', true);
    }
}
