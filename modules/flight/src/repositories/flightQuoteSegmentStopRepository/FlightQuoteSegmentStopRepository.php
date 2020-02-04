<?php

namespace modules\flight\src\repositories\flightQuoteSegmentStopRepository;

use modules\flight\models\FlightQuoteSegmentStop;
use sales\repositories\Repository;

/**
 * Class FlightQuoteSegmentStopRepository
 * @package modules\flight\src\repositories\flightQuoteSegmentStopRepository
 */
class FlightQuoteSegmentStopRepository extends Repository
{

	/**
	 * @param FlightQuoteSegmentStop $flightQuoteSegmentStop
	 * @return int
	 */
	public function save(FlightQuoteSegmentStop $flightQuoteSegmentStop): int
	{
		if (!$flightQuoteSegmentStop->save()) {
			throw new \RuntimeException($flightQuoteSegmentStop->getErrorSummary(false)[0]);
		}
		return $flightQuoteSegmentStop->qss_id;
	}
}