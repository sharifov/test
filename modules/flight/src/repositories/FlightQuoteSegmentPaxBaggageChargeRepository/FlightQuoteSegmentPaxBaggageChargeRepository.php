<?php

namespace modules\flight\src\repositories\FlightQuoteSegmentPaxBaggageChargeRepository;

use modules\flight\models\FlightQuoteSegmentPaxBaggageCharge;
use sales\repositories\Repository;

class FlightQuoteSegmentPaxBaggageChargeRepository extends Repository
{
	/**
	 * @param FlightQuoteSegmentPaxBaggageCharge $baggageCharge
	 * @return int
	 */
	public function save(FlightQuoteSegmentPaxBaggageCharge $baggageCharge): int
	{
		if (!$baggageCharge->save()) {
			throw new \RuntimeException($baggageCharge->getErrorSummary(false)[0]);
		}
		return $baggageCharge->qsbc_id;
	}
}