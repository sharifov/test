<?php

namespace modules\flight\src\repositories\flightQuoteRepository;

use modules\flight\models\FlightQuote;
use sales\dispatchers\EventDispatcher;
use sales\repositories\Repository;

/**
 * Class FlightQuoteRepository
 * @package modules\flight\src\repositories\flightQuoteRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class FlightQuoteRepository extends Repository
{
	/**
	 * @var EventDispatcher
	 */
	private $eventDispatcher;

	/**
	 * FlightQuoteRepository constructor.
	 * @param EventDispatcher $eventDispatcher
	 */
	public function __construct(EventDispatcher $eventDispatcher)
	{
		$this->eventDispatcher = $eventDispatcher;
	}

	/**
	 * @param FlightQuote $flightQuote
	 * @return int
	 */
	public function save(FlightQuote $flightQuote): int
	{
		if (!$flightQuote->save()) {
			throw new \RuntimeException($flightQuote->getErrorSummary(false)[0]);
		}
		$this->eventDispatcher->dispatchAll($flightQuote->releaseEvents());
		return $flightQuote->fq_id;
	}
}