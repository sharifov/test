<?php

namespace modules\flight\src\repositories\flightRequest;

use modules\flight\models\FlightRequest;

/**
 * Class FlightRequestRepository
 */
class FlightRequestRepository
{
    public function save(FlightRequest $model): FlightRequest
    {
        if (!$model->save(false)) {
            throw new \RuntimeException('FlightRequest save is failed');
        }
        return $model;
    }
}
