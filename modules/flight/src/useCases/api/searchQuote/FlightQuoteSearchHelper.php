<?php

namespace modules\flight\src\useCases\api\searchQuote;

use common\models\Airline;
use common\models\Airport;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteSearchHelper
 * @package modules\flight\src\useCases\api\searchQuote
 */
class FlightQuoteSearchHelper
{
	/**
	 * @param $result
	 * @return array
	 */
	public static function getAirlineLocationInfo($result)
	{
		$airlinesIata = [];
		$locationsIata = [];
		if(isset($result['results'])){
			foreach ($result['results'] as $resItem){
				if(!in_array($resItem['validatingCarrier'], $airlinesIata)){
					$airlinesIata[] = $resItem['validatingCarrier'];
				}
				foreach ($resItem['trips'] as $trip){
					foreach ($trip['segments'] as $segment){
						if(!in_array($segment['operatingAirline'], $airlinesIata)){
							$airlinesIata[] = $segment['operatingAirline'];
						}
						if(!in_array($segment['marketingAirline'], $airlinesIata)){
							$airlinesIata[] = $segment['marketingAirline'];
						}
						if(!in_array($segment['departureAirportCode'], $locationsIata)){
							$locationsIata[] = $segment['departureAirportCode'];
						}
						if(!in_array($segment['arrivalAirportCode'], $locationsIata)){
							$locationsIata[] = $segment['arrivalAirportCode'];
						}
					}
				}
			}
		}

		$airlines = Airline::getAirlinesListByIata($airlinesIata);
		$locations = Airport::getAirportListByIata($locationsIata);

		return ['airlines' => $airlines, 'locations' => $locations];
	}

	/**
	 * @param array $quotes
	 * @return array
	 */
	public static function formatQuoteData(array $quotes): array
	{
		self::getQuotePriceRange($quotes);

		foreach ($quotes['results'] as $key => $quote) {
			$quotes['results'][$key]['price'] = self::getQuotePrice($quote);

			$preSegment = null;
			$baggagePerSegment = [];
			$freeBaggage = false;
			$airportChange = false;
			$technicalStopCnt = 0;
			$time = [];
			$stops = [];
			$totalDuration = [];
			$bagFilter = '';

			foreach ($quote['trips'] as $trip) {
				if(isset($trip['duration'])){
					$totalDuration[] = $trip['duration'];
					$quotes['totalDuration'][] = $trip['duration'];
//					$totalDurationSum += $trip['duration'];
				}
				$stopCnt = count($trip['segments']) - 1;

				foreach ($trip['segments'] as $segment) {
					if (isset($segment['stop']) && $segment['stop'] > 0) {
						$stopCnt += $segment['stop'];
						$technicalStopCnt += $segment['stop'];
					}

					if ($preSegment !== null && $segment['departureAirportCode'] != $preSegment['arrivalAirportCode']) {
						$airportChange = true;
					}

					if (isset($segment['baggage']) && $freeBaggage === false) {
						foreach ($segment['baggage'] as $baggage) {
							if (isset($baggage['allowPieces'])) {
								$baggagePerSegment[] = $baggage['allowPieces'];
							}
						}
					}
					$preSegment = $segment;
				}

				$firstSegment = $trip['segments'][0];
				$lastSegment = end($trip['segments']);
				$time[] = ['departure' => $firstSegment['departureTime'],'arrival' => $lastSegment['arrivalTime']];
				$stops[] = $stopCnt;
			}

			if (!empty($baggagePerSegment)) {
				if (min($baggagePerSegment) == 1) {
					$bagFilter = 1;
				} elseif ((min($baggagePerSegment) == 2)) {
					$bagFilter = 2;
				}
			}

			$quotes['results'][$key]['stops'] = $stops;
			$quotes['results'][$key]['time'] = $time;
			$quotes['results'][$key]['bagFilter'] = $bagFilter ?? '';
			$quotes['results'][$key]['airportChange'] = $airportChange;
			$quotes['results'][$key]['technicalStopCnt'] = $technicalStopCnt;
			$quotes['results'][$key]['duration'] = $totalDuration;
		}

		return $quotes;
	}

	/**
	 * @param array $quotes
	 */
	private static function getQuotePriceRange(array &$quotes): void
	{
		$minPrice = $quotes['results'][0]['prices']['totalPrice'];
		if(isset($quotes['results'][0]['passengers']['ADT'])){
			$minPrice = $quotes['results'][0]['passengers']['ADT']['price'];
		}elseif (isset($quotes['results'][0]['passengers']['CHD'])){
			$minPrice = $quotes['results'][0]['passengers']['CHD']['price'];
		}elseif (isset($quotes['results'][0]['passengers']['INF'])){
			$minPrice = $quotes['results'][0]['passengers']['INF']['price'];
		}
		$lastResult = end($quotes['results']);
		$maxPrice = $lastResult['prices']['totalPrice'];
		if(isset($lastResult['passengers']['ADT'])){
			$maxPrice = $lastResult['passengers']['ADT']['price'];
		}elseif (isset($lastResult['passengers']['CHD'])){
			$maxPrice = $lastResult['passengers']['CHD']['price'];
		}elseif (isset($lastResult['passengers']['INF'])){
			$maxPrice = $lastResult['passengers']['INF']['price'];
		}

		$quotes['minPrice'] = $minPrice;
		$quotes['maxPrice'] = $maxPrice;
	}

	/**
	 * @param array $quote
	 * @return mixed
	 */
	private static function getQuotePrice(array $quote)
	{
		$price = $quote['prices']['totalPrice'];
		if(isset($quote['passengers']['ADT'])){
			$price = $quote['passengers']['ADT']['price'];
		}elseif (isset($quote['passengers']['CHD'])){
			$price = $quote['passengers']['CHD']['price'];
		}elseif (isset($quote['passengers']['INF'])){
			$price = $quote['passengers']['INF']['price'];
		}
		return $price;
	}

}