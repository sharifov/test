<?php
namespace modules\flight\src\repositories\flightQuoteSegment;

use modules\flight\models\FlightQuoteSegment;
use sales\repositories\Repository;

/**
 * Class FlightQuoteSegmentRepository
 * @package modules\flight\src\repositories\flightQuoteSegment
 */
class FlightQuoteSegmentRepository extends Repository
{

	/**
	 * @param FlightQuoteSegment $flightQuoteSegment
	 * @return int
	 */
	public function save(FlightQuoteSegment $flightQuoteSegment): int
	{
		if (!$flightQuoteSegment->save()) {
			throw new \RuntimeException($flightQuoteSegment->getErrorSummary(false)[0]);
		}
		return $flightQuoteSegment->fqs_id;
	}
}