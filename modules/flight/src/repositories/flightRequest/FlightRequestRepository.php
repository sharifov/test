<?php

namespace modules\flight\src\repositories\flightRequest;

use modules\flight\models\FlightRequest;
use modules\flight\src\exceptions\FlightCodeException;
use sales\helpers\ErrorsToStringHelper;

/**
 * Class FlightRequestRepository
 */
class FlightRequestRepository
{
    public function save(FlightRequest $model): FlightRequest
    {
        if (!$model->save(false)) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model), FlightCodeException::FLIGHT_REQUEST_ENTITY_SAVING_FAILED);
        }
        return $model;
    }
}
