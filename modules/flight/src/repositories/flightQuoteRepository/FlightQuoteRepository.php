<?php

namespace modules\flight\src\repositories\flightQuoteRepository;

use modules\flight\models\FlightQuote;
use sales\repositories\Repository;

class FlightQuoteRepository extends Repository
{
	/**
	 * @param FlightQuote $flightQuote
	 * @return int
	 */
	public function save(FlightQuote $flightQuote): int
	{
		if (!$flightQuote->save()) {
			throw new \RuntimeException($flightQuote->getErrorSummary(false)[0]);
		}
		return $flightQuote->fq_id;
	}
}