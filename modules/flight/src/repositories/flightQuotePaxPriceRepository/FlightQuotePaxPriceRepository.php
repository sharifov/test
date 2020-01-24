<?php

namespace modules\flight\src\repositories\flightQuotePaxPriceRepository;

use modules\flight\models\FlightQuotePaxPrice;
use sales\repositories\Repository;

/**
 * Class FlightQuotePaxPriceRepository
 * @package modules\flight\src\repositories\flightQuotePaxPriceRepository
 */
class FlightQuotePaxPriceRepository extends Repository
{
	/**
	 * @param FlightQuotePaxPrice $flightQuotePaxPrice
	 * @return int
	 */
	public function save(FlightQuotePaxPrice $flightQuotePaxPrice): int
	{
		if (!$flightQuotePaxPrice->save()) {
			throw new \RuntimeException($flightQuotePaxPrice->getErrorSummary(false)[0]);
		}
		return $flightQuotePaxPrice->qpp_id;
	}
}