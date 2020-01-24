<?php

namespace modules\flight\src\repositories\flightQuoteTripRepository;

use modules\flight\models\FlightQuoteTrip;
use sales\repositories\Repository;

/**
 * Class FlightQuoteTripRepository
 * @package modules\flight\src\repositories\flightQuoteTripRepository
 */
class FlightQuoteTripRepository extends Repository
{
	/**
	 * @param FlightQuoteTrip $flightQuoteTrip
	 * @return int
	 */
	public function save(FlightQuoteTrip $flightQuoteTrip): int
	{
		if (!$flightQuoteTrip->save()) {
			throw new \RuntimeException($flightQuoteTrip->getErrorSummary(false)[0]);
		}
		return $flightQuoteTrip->fqt_id;
	}
}