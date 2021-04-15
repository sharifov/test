<?php

namespace modules\flight\src\repositories\flightQuoteBooking;

use modules\flight\models\FlightQuoteBooking;

/**
 * Class FlightQuoteBookingRepository
 */
class FlightQuoteBookingRepository
{
    public function save(FlightQuoteBooking $model): int
    {
        if (!$model->save(false)) {
            throw new \RuntimeException('FlightQuoteBooking save is failed');
        }
        return $model->fqb_id;
    }
}
