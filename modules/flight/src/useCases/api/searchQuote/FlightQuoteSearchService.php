<?php


namespace modules\flight\src\useCases\api\searchQuote;


use modules\flight\components\api\ApiFlightQuoteSearchService;
use modules\flight\models\Flight;

/**
 * Class FlightQuoteSearchService
 * @package modules\flight\src\useCases\api\searchQuote
 *
 * @property ApiFlightQuoteSearchService $apiFlightQuoteSearchService
 */
class FlightQuoteSearchService
{
	/**
	 * @var ApiFlightQuoteSearchService
	 */
	private $apiFlightQuoteSearchService;

	/**
	 * FlightQuoteSearchService constructor.
	 * @param ApiFlightQuoteSearchService $apiFlightQuoteSearchService
	 */
	public function __construct(ApiFlightQuoteSearchService $apiFlightQuoteSearchService)
	{
		$this->apiFlightQuoteSearchService = $apiFlightQuoteSearchService;
	}

	/**
	 * @param Flight $flight
	 * @param null $gdsCode
	 * @return array|mixed
	 */
	public function search(Flight $flight, $gdsCode = null)
	{
		$fl = [];

		if (!$flight->flightSegments) {
			throw new \DomainException('Flight request has no segments; Fill flight request data;');
		}

		$params = [
			'cabin' => $flight->getCabinRealCode($flight->fl_cabin_class),
			'cid' => 'SAL101',
			'adt' => $flight->fl_adults,
			'chd' => $flight->fl_children,
			'inf' => $flight->fl_infants,
		];

		if ($gdsCode) {
			$params['gds'] = $gdsCode;
		}

		foreach ($flight->flightSegments as $segment) {
			$fl[] = [
				'o' => $segment->fs_origin_iata,
				'd' => $segment->fs_destination_iata,
				'dt' => $segment->fs_departure_date
			];
		}
		$params['fl'] = $fl;

		return $this->apiFlightQuoteSearchService->search($params)['data'];
	}
}