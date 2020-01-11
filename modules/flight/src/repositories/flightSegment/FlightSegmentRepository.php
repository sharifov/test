<?php

namespace modules\flight\src\repositories\flightSegment;

use modules\flight\models\FlightSegment;
use modules\flight\src\exceptions\FlightCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class FlightSegmentRepository
 * @package modules\flight\src\repositories\flightSegment
 *
 * @property EventDispatcher $eventDispatcher
 */
class FlightSegmentRepository extends Repository
{

	public function __construct(EventDispatcher $eventDispatcher)
	{
		$this->eventDispatcher = $eventDispatcher;
	}

	/**
	 * @param $id
	 * @return FlightSegment
	 */
	public function find($id): FlightSegment
	{
		if ($segment = FlightSegment::findOne($id)) {
			return $segment;
		}
		throw new NotFoundException('FlightSegment is not found.', FlightCodeException::SEGMENT_NOT_FOUND);
	}

	/**
	 * @param FlightSegment $segment
	 * @return int
	 */
	public function save(FlightSegment $segment): int
	{
		if (!$segment->save(false)) {
			throw new \RuntimeException('Saving error.', FlightCodeException::SEGMENT_SAVE);
		}
		$this->eventDispatcher->dispatchAll($segment->releaseEvents());
		return $segment->fs_id;
	}

	/**
	 * @param FlightSegment $segment
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function remove(FlightSegment $segment): void
	{
		if (!$segment->delete()) {
			throw new \RuntimeException('Removing error.', FlightCodeException::SEGMENT_REMOVE);
		}
		$this->eventDispatcher->dispatchAll($segment->releaseEvents());
	}

	/**
	 * @param array $segments
	 * @param array $newIds
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function removeOld(array $segments, array $newIds): void
	{
		/** @var FlightSegment $segment */
		foreach ($segments as $segment) {
			if (!in_array($segment->fs_id, $newIds)) {
				$this->remove($segment);
			}
		}
	}
}