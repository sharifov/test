<?php

namespace modules\flight\src\repositories\flightQuotePaxPriceRepository;

use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\src\entities\flightQuotePaxPrice\FlightQuotePaxPriceQuery;
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

	/**
	 * @param int $fqId
	 * @param int $paxCode
	 * @return array|FlightQuotePaxPrice|null
	 */
	public function findByIdAndCode(int $fqId, int $paxCode)
	{
		$flightQuotePaxPrice = FlightQuotePaxPriceQuery::findByFlightIdAndPaxCodeId($fqId, $paxCode);
		if (!$flightQuotePaxPrice) {
			throw new \RuntimeException('Pax Price not found');
		}
		return $flightQuotePaxPrice;
	}
}