<?php

namespace modules\flight\src\repositories\flightPaxRepository;

use modules\flight\models\FlightPax;

class FlightPaxRepository
{
    /**
     * @param FlightPax $flightPax
     * @return int
     */
    public function save(FlightPax $flightPax): int
    {
        if (!$flightPax->save()) {
            throw new \RuntimeException($flightPax->getErrorSummary(false)[0]);
        }
        return $flightPax->fp_id;
    }
}
