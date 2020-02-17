<?php

namespace modules\flight\src\listeners;

use modules\flight\src\events\FlightRequestUpdateEvent;
use modules\flight\src\repositories\flightQuoteStatusLogRepository\FlightQuoteStatusLogRepository;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use sales\repositories\product\ProductQuoteRepository;

/**
 * Class FlightRequestUpdateEventListener
 * @package modules\flight\src\listeners
 *
 * @property FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository
 * @property ProductQuoteRepository $productQuoteRepository
 */
class FlightRequestUpdateEventListener
{
	/**
	 * @var FlightQuoteStatusLogRepository
	 */
	private $flightQuoteStatusLogRepository;
	/**
	 * @var ProductQuoteRepository
	 */
	private $productQuoteRepository;

	/**
	 * @param FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository
	 * @param ProductQuoteRepository $productQuoteRepository
	 */
	public function __construct(FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository, ProductQuoteRepository $productQuoteRepository)
	{
		$this->flightQuoteStatusLogRepository = $flightQuoteStatusLogRepository;
		$this->productQuoteRepository = $productQuoteRepository;
	}

	/**
	 * @param FlightRequestUpdateEvent $event
	 */
	public function handle(FlightRequestUpdateEvent $event): void
	{
		foreach ($event->flight->flightQuotes as $quote) {

			foreach ($quote->flightQuoteStatusLogs as $quoteStatusLog) {

				if (!$quoteStatusLog->isApplied()) {
					$quoteStatusLog->decline();

					$productQuote = $quoteStatusLog->qslFlightQuote->fqProductQuote;

					try {
						$this->flightQuoteStatusLogRepository->save($quoteStatusLog);

						if ($productQuote) {
							$productQuote->declined();
							$this->productQuoteRepository->save($productQuote);
						}
					} catch (\Exception $e) {
						\Yii::$app->errorHandler->logException($e);
					}
				}
			}
		}
	}
}