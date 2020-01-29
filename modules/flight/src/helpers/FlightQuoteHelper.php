<?php

namespace modules\flight\src\helpers;

use modules\flight\models\FlightQuote;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;

class FlightQuoteHelper
{
	/**
	 * @param $key
	 * @return string
	 */
	public static function generateHashQuoteKey($key): string
	{
		return md5($key);
	}

	/**
	 * @param array $flightQuote
	 * @param $quoteKey
	 * @return bool
	 */
	public static function isQuoteAssignedToFlight(array $flightQuote, $quoteKey): bool
	{
		return in_array(self::generateHashQuoteKey($quoteKey), $flightQuote, false);
	}

	/**
	 * @param FlightQuote $flightQuote
	 * @return array
	 */
	public static function getTicketSegments(FlightQuote $flightQuote): array
	{
		$segments = [];

		if($flightQuote->fq_origin_search_data) {
			$dataArr = @json_decode($flightQuote->fq_origin_search_data, true);

			if($dataArr && isset($dataArr['tickets'])) {
				$ticketsArr = $dataArr['tickets'];
				$ticketNr = 1;
				foreach ($ticketsArr as $ticket) {

					if(!empty($ticket['trips'])) {

						foreach ($ticket['trips'] as $trip) {

							if(!empty($trip['segmentIds'])) {
								foreach ($trip['segmentIds'] as $segmentId) {
									$segments[$trip['tripId']][$segmentId] = $ticketNr;
								}
							}
						}
					}

					$ticketNr ++;
				}
			}
		}

		return $segments;
	}

	/**
	 * @param FlightQuote $flightQuote
	 * @param $tripNr
	 * @param $segmentNr
	 * @return mixed|null
	 */
	public static function getTicketId(FlightQuote $flightQuote, $tripNr, $segmentNr)
	{
		$ticketSegments = self::getTicketSegments($flightQuote);
		return $ticketSegments[$tripNr][$segmentNr] ?? null;
	}

	/**
	 * @param array $quote
	 * @return string
	 */
	public static function getItineraryDump(array $quote): string
	{
		$segments = [];

		foreach ($quote['trips'] as $trip){
			foreach ($trip['segments'] as $segment) {
				$segments[] = (new ItineraryDumpDTO($segment));
			}
		}
		return implode("\n", self::createDump($segments));
	}

	/**
	 * @param array $itineraries
	 * @return array
	 */
	public static function createDump(array $itineraries): array
	{
		$nr = 1;
		$dump = [];
		foreach ($itineraries as $itinerary) {
			$daysName = self::getDayName($itinerary->departureTime, $itinerary->arrivalTime);

			$segment = $nr++ . self::addSpace(1);
			$segment .= $itinerary->airlineCode;
			$segment .= self::addSpace(4 - strlen($itinerary->flightNumber)) . $itinerary->flightNumber;
			$segment .= $itinerary->bookingClass . self::addSpace(1);

			$departureDate = strtoupper(date('dM', strtotime($itinerary->departureTime)));
			$segment .= $departureDate . self::addSpace(1);

			$segment .= $itinerary->departureAirportCode . $itinerary->destinationAirportCode . self::addSpace(1);

			$segment .= empty($itinerary->statusCode) ? '' : strtoupper($itinerary->statusCode) . self::addSpace(1);

			$time = substr(str_replace(' ', '', str_replace(':', '', date('g:i A', strtotime($itinerary->departureTime)))), 0, -1);
			$segment .= self::addSpace(5 - strlen($time)) . $time . self::addSpace(1);
			$time = substr(str_replace(' ', '', str_replace(':', '', date('g:i A', strtotime($itinerary->arrivalTime)))), 0, -1);
			$segment .= (strlen($daysName) === 2)
				? self::addSpace(5 - strlen($time)) . $time . self::addSpace(1)
				: self::addSpace(5 - strlen($time)) . $time . '+' . self::addSpace(1);

			$arrivalDate = strtoupper(date('dM', strtotime($itinerary->arrivalTime)));
			$segment .= ($arrivalDate != $departureDate)
				? $arrivalDate . self::addSpace(1) : '';

			$segment .= $daysName;

			if ($itinerary->operationAirlineCode) {
				$segment .= ' OPERATED BY ' . $itinerary->operationAirlineCode;
			}

			$dump[] = $segment;
		}
		return $dump;
	}

	/**
	 * @param string $departureTime
	 * @param string $arrivalTime
	 * @return false|string
	 */
	private static function getDayName(string $departureTime, string $arrivalTime): string
	{
		$departureDay = strtoupper(substr(date('D', strtotime($departureTime)), 0, -1));
		$arrivalDay = strtoupper(substr(date('D', strtotime($arrivalTime)), 0, -1));
		if (strcmp($departureDay, $arrivalDay) === 0) {
			return $departureDay;
		}
		return $departureDay . '/' . $arrivalDay;
	}

	/**
	 * @param int $n
	 * @return string
	 */
	private static function addSpace(int $n): string
	{
		$space = '';
		for ($i = 0; $i < $n; $i++) {
			$space .= '&nbsp; ';
		}
		return $space;
	}
}