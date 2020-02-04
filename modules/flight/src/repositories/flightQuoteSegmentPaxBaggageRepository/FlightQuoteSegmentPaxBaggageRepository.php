<?php

namespace modules\flight\src\repositories\flightQuoteSegmentPaxBaggageRepository;

use modules\flight\models\FlightQuoteSegmentPaxBaggage;
use sales\repositories\Repository;

/**
 * Class FlightQuoteSegmentPaxBaggageRepository
 * @package modules\flight\src\repositories\flightQuoteSegmentPaxBaggage
 */
class FlightQuoteSegmentPaxBaggageRepository extends Repository
{
	/**
	 * @param FlightQuoteSegmentPaxBaggage $flightQuoteSegmentPaxBaggage
	 * @return int
	 */
	public function save(FlightQuoteSegmentPaxBaggage $flightQuoteSegmentPaxBaggage): int
	{
		if (!$flightQuoteSegmentPaxBaggage->save()) {
			throw new \RuntimeException($flightQuoteSegmentPaxBaggage->getErrorSummary(false)[0]);
		}
		return $flightQuoteSegmentPaxBaggage->qsb_id;
 	}
}