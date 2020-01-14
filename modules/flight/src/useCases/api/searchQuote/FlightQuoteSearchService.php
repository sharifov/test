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
		$keyCache = $flight->generateQuoteSearchKeyCache();
		$quotes = \Yii::$app->cache->get($keyCache);

		if ($quotes === false) {
			$fl = [];

			$params = [
				'cabin' => $flight->getCabinRealCode($flight->fl_cabin_class),
				'cid' => 'SAL101',
				'adt' => $flight->fl_adults,
				'chd' => $flight->fl_children,
				'inf' => $flight->fl_infants,
			];

			if($gdsCode) {
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

			$quotes = $this->apiFlightQuoteSearchService->search($params)['data'];
			if ($quotes) {
				\Yii::$app->cache->set($keyCache, $quotes, 600);
			}
		}

		return $quotes;
	}
}