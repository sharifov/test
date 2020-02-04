<?php

namespace modules\flight\src\repositories\flightPaxRepository;

use modules\flight\models\FlightPax;
use sales\repositories\Repository;

class FlightPaxRepository extends Repository
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