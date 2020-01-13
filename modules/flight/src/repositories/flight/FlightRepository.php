<?php

namespace modules\flight\src\repositories\flight;

use modules\flight\models\Flight;
use modules\flight\src\exceptions\FlightCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class FlightRepository
 * @package modules\flight\src\repositories\flight
 *
 * @property EventDispatcher $eventDispatcher
 */
class FlightRepository extends Repository
{
	private $eventDispatcher;

	public function __construct(EventDispatcher $eventDispatcher)
	{
		$this->eventDispatcher = $eventDispatcher;
	}

	/**
	 * @param int $id
	 * @return Flight
	 */
	public function find(int $id): Flight
	{
		if ($flight = Flight::findOne($id)) {
			return $flight;
		}
		throw new NotFoundException('Flight is not found', FlightCodeException::FLIGHT_NOT_FOUND);
	}

	/**
	 * @param Flight $flight
	 * @return int
	 */
	public function save(Flight $flight): int
	{
		$flight->disableAREvents();

		if (!$flight->save(false)) {
			throw new \RuntimeException('Saving error', FlightCodeException::FLIGHT_SAVE);
		}
		$this->eventDispatcher->dispatchAll($flight->releaseEvents());
		return $flight->fl_id;
	}
}