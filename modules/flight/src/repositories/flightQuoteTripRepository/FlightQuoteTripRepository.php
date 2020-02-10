<?php

namespace modules\flight\src\repositories\flightQuoteTripRepository;

use modules\flight\models\FlightQuoteTrip;
use modules\flight\src\exceptions\FlightCodeException;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class FlightQuoteTripRepository
 * @package modules\flight\src\repositories\flightQuoteTripRepository
 */
class FlightQuoteTripRepository extends Repository
{
    public function find(int $id): FlightQuoteTrip
    {
        if ($segment = FlightQuoteTrip::findOne($id)) {
            return $segment;
        }
        throw new NotFoundException('Flight Quote Trip is not found', FlightCodeException::FLIGHT_QUOTE_TRIP_NOT_FOUND);
    }

	public function save(FlightQuoteTrip $trip): int
	{
		if (!$trip->save()) {
			throw new \RuntimeException($trip->getErrorSummary(false)[0]);
		}
		return $trip->fqt_id;
	}

    public function remove(FlightQuoteTrip $trip): void
    {
        if (!$trip->delete()) {
            throw new \RuntimeException('Removing error', FlightCodeException::FLIGHT_QUOTE_TRIP_REMOVE);
        }
    }
}
