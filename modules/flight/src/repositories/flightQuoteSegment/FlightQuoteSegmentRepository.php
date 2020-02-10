<?php
namespace modules\flight\src\repositories\flightQuoteSegment;

use modules\flight\models\FlightQuoteSegment;
use modules\flight\src\exceptions\FlightCodeException;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class FlightQuoteSegmentRepository
 */
class FlightQuoteSegmentRepository extends Repository
{
    public function find(int $id): FlightQuoteSegment
    {
        if ($segment = FlightQuoteSegment::findOne($id)) {
            return $segment;
        }
        throw new NotFoundException('Flight Quote Segment is not found', FlightCodeException::FLIGHT_QUOTE_SEGMENT_NOT_FOUND);
    }

	public function save(FlightQuoteSegment $segment): int
	{
		if (!$segment->save()) {
			throw new \RuntimeException($segment->getErrorSummary(false)[0]);
		}
		return $segment->fqs_id;
	}

    public function remove(FlightQuoteSegment $segment): void
    {
        if (!$segment->delete()) {
            throw new \RuntimeException('Removing error', FlightCodeException::FLIGHT_QUOTE_SEGMENT_REMOVE);
        }
    }
}
