<?php

namespace modules\flight\src\repositories\flightQuoteFlight;

use modules\flight\models\FlightQuoteFlight;

/**
 * Class FlightQuoteFlightRepository
 */
class FlightQuoteFlightRepository
{
    public function save(FlightQuoteFlight $model): int
    {
        if (!$model->save(false)) {
            throw new \RuntimeException('FlightQuoteFlight save is failed');
        }
        return $model->fqf_id;
    }
}
