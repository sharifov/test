<?php

namespace modules\flight\src\repositories\flightQuoteRepository;

use modules\flight\models\FlightQuote;
use modules\flight\src\exceptions\FlightCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;
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

    public function find(?int $id): FlightQuote
    {
        if ($quote = FlightQuote::findOne($id)) {
            return $quote;
        }
        throw new NotFoundException('Flight quote is not found', FlightCodeException::FLIGHT_QUOTE_NOT_FOUND);
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

    public function remove(FlightQuote $quote): void
    {
        if (!$quote->delete()) {
            throw new \RuntimeException('Removing error', FlightCodeException::FLIGHT_QUOTE_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($quote->releaseEvents());
    }
}
