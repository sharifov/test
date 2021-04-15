<?php

namespace modules\flight\src\repositories\flightQuoteBookingAirline;

use modules\flight\models\FlightQuoteBookingAirline;

/**
 * Class FlightQuoteBookingRepository
 */
class FlightQuoteBookingAirlineRepository
{
    public function save(FlightQuoteBookingAirline $model): int
    {
        if (!$model->save(false)) {
            throw new \RuntimeException('FlightQuoteBookingAirline save is failed');
        }
        return $model->fqba_id;
    }
}
