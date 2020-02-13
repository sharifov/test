<?php

namespace modules\flight\src\repositories\flightQuoteStatusLogRepository;

use modules\flight\models\FlightQuoteStatusLog;
use sales\dispatchers\EventDispatcher;
use sales\repositories\Repository;

/**
 * Class FlightQuoteStatusLogRepository
 * @package modules\flight\src\repositories\flightQuoteStatusLogRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class FlightQuoteStatusLogRepository extends Repository
{
	/**
	 * @var EventDispatcher
	 */
	private $eventDispatcher;

	public function __construct(EventDispatcher $eventDispatcher)
	{
		$this->eventDispatcher = $eventDispatcher;
	}
	
	/**
	 * @param FlightQuoteStatusLog $quoteStatusLog
	 * @return int
	 */
	public function save(FlightQuoteStatusLog $quoteStatusLog): int
	{
		if (!$quoteStatusLog->save(false)) {
			throw new \RuntimeException('Saving error');
		}
		$this->eventDispatcher->dispatchAll($quoteStatusLog->releaseEvents());
		return $quoteStatusLog->qsl_id;
	}
}