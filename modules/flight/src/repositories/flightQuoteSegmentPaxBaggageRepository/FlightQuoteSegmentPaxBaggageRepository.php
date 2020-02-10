<?php

namespace modules\flight\src\repositories\flightQuoteSegmentPaxBaggageRepository;

use modules\flight\models\FlightQuoteSegmentPaxBaggage;
use modules\flight\src\exceptions\FlightCodeException;
use sales\repositories\Repository;

/**
 * Class FlightQuoteSegmentPaxBaggageRepository
 */
class FlightQuoteSegmentPaxBaggageRepository extends Repository
{
	public function save(FlightQuoteSegmentPaxBaggage $baggage): int
	{
		if (!$baggage->save()) {
			throw new \RuntimeException($baggage->getErrorSummary(false)[0]);
		}
		return $baggage->qsb_id;
 	}

    public function remove(FlightQuoteSegmentPaxBaggage $baggage): void
    {
        if (!$baggage->delete()) {
            throw new \RuntimeException('Removing error', FlightCodeException::FLIGHT_QUOTE_SEGMENT_PAX_BAGGAGE_REMOVE);
        }
    }
}
