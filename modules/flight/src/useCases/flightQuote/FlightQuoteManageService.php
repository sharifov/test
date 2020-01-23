<?php


namespace modules\flight\src\useCases\flightQuote;


use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use sales\services\TransactionManager;

class FlightQuoteManageService
{
	/**
	 * @var FlightQuoteRepository
	 */
	private $flightQuoteRepository;
	/**
	 * @var TransactionManager
	 */
	private $transactionManager;

	/**
	 * FlightQuoteService constructor.
	 * @param FlightQuoteRepository $flightQuoteRepository
	 * @param TransactionManager $transactionManager
	 */
	public function __construct(FlightQuoteRepository $flightQuoteRepository, TransactionManager $transactionManager)
	{
		$this->flightQuoteRepository = $flightQuoteRepository;
		$this->transactionManager = $transactionManager;
	}

	/**
	 * @param Flight $flight
	 * @param array $quote
	 * @throws \Throwable
	 */
	public function create(Flight $flight, array $quote)
	{
		echo '<pre>';print_r($quote);die;

		$this->transactionManager->wrap(static function () use ($flight, $quote) {
			$newFlightQuote = FlightQuote::create();

			$flightQuote = $this->flightQuoteRepository->create($newFlightQuote);

		});



	}
}