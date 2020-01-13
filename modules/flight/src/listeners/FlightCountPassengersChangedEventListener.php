<?php

namespace modules\flight\src\listeners;

use modules\flight\src\events\FlightCountPassengersChangedEvent;
use modules\flight\src\repositories\flightQuoteStatusLogRepository\FlightQuoteStatusLogRepository;

/**
 * Class FlightCountPassengersChangedEventListener
 * @package modules\flight\src\listeners
 *
 * @property FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository
 */
class FlightCountPassengersChangedEventListener
{
	private $flightQuoteStatusLogRepository;

	/**
	 * @param FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository
	 */
	public function __construct(FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository)
	{
		$this->flightQuoteStatusLogRepository = $flightQuoteStatusLogRepository;
	}

	/**
	 * @param FlightCountPassengersChangedEvent $event
	 */
	public function handle(FlightCountPassengersChangedEvent $event): void
	{
		foreach ($event->flight->flightQuotes as $quote) {

			foreach ($quote->flightQuoteStatusLogs as $quoteStatusLog) {

				if (!$quoteStatusLog->isApplied()) {
					$quoteStatusLog->decline();
					try {
						$this->flightQuoteStatusLogRepository->save($quoteStatusLog);
					} catch (\Exception $e) {
						\Yii::$app->errorHandler->logException($e);
					}
				}
			}
		}
	}
}