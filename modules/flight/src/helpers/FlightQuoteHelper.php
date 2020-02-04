<?php

namespace modules\flight\src\helpers;

use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\helpers\product\ProductQuoteHelper;
use yii\data\ActiveDataProvider;

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
	public static function getPricesData(FlightQuote $flightQuote): array
	{
		$prices = [];
		$service_fee_percent = $flightQuote->getServiceFeePercent();
		$defData = [
			'fare' => 0,
			'taxes' => 0,
			'net' => 0, // fare + taxes
			'tickets' => 0,
			'mark_up' => 0,
			'extra_mark_up' => 0,
			'service_fee' => 0,
			'selling' => 0, //net + mark_up + extra_mark_up + service_fee
			'service_fee_sum' => 0,
			'client_selling' => 0
		];
		$total = $defData;

		$paxCodeId = null;
		foreach ($flightQuote->flightQuotePaxPrices as $price){
			$paxCode = FlightPax::getPaxTypeById($price->qpp_flight_pax_code_id);
			if($paxCodeId !== $price->qpp_flight_pax_code_id) {
				$prices[$paxCode] = $defData;
				$paxCodeId = $price->qpp_flight_pax_code_id;
			}
			$prices[$paxCode]['fare'] += $price->qpp_fare;
			$prices[$paxCode]['taxes'] += $price->qpp_tax;
			$prices[$paxCode]['net'] = (float)(($prices[$paxCode]['fare'] + $prices[$paxCode]['taxes']) * $price->qpp_cnt);
			$prices[$paxCode]['tickets'] += $price->qpp_cnt;
			$prices[$paxCode]['mark_up'] += $price->qpp_system_mark_up * $price->qpp_cnt;
			$prices[$paxCode]['extra_mark_up'] += $price->qpp_agent_mark_up * $price->qpp_cnt;
			$prices[$paxCode]['selling'] = ($prices[$paxCode]['net'] + $prices[$paxCode]['mark_up'] + $prices[$paxCode]['extra_mark_up']);
			$prices[$paxCode]['service_fee'] = ($prices[$paxCode]['selling'] * $service_fee_percent / 100);
			$prices[$paxCode]['selling'] += $prices[$paxCode]['service_fee'];
			$prices[$paxCode]['selling'] = ProductQuoteHelper::roundPrice($prices[$paxCode]['selling']);
			$prices[$paxCode]['client_selling'] = ProductQuoteHelper::roundPrice($prices[$paxCode]['selling'] * $flightQuote->fqProductQuote->pq_client_currency_rate);
		}

		foreach ($prices as $key => $price){
			$total['tickets'] += $price['tickets'];
			$total['net'] += $price['net'];
			$total['mark_up'] += $price['mark_up'];
			$total['extra_mark_up'] += $price['extra_mark_up'];
			$total['selling'] += $price['selling'];
			$total['service_fee_sum'] += ProductQuoteHelper::roundPrice($price['service_fee']);
			$total['client_selling'] += $price['client_selling'];
		}

		return [
			'prices' => $prices,
			'total' => $total,
			'service_fee_percent' => $service_fee_percent,
			'service_fee' => ($service_fee_percent > 0)?$total['selling'] * $service_fee_percent / 100:0,
			'processing_fee' => $flightQuote->getProcessingFee()
		];
	}

	/**
	 * @param array $priceData
	 * @return string
	 */
	public static function getEstimationProfitText(array $priceData): string
	{
		$data = [];
		/* if(isset($priceData['service_fee']) && $priceData['service_fee'] > 0){
			$data[] = '<span class="text-danger">Merchant fee: -'.round($priceData['service_fee'],2).'$</span>';
		} */
		if(isset($priceData['processing_fee']) && $priceData['processing_fee'] > 0){
			$data[] = '<span class="text-danger">Processing fee: -'.ProductQuoteHelper::roundPrice($priceData['processing_fee']).'$</span>';
		}

		return (empty($data))?'-':implode('<br/>', $data);
	}

	/**
	 * @param array $priceData
	 * @return false|float
	 */
	public static function getEstimationProfit(array $priceData)
	{
		$profit = 0;
		$markUp = $priceData['total']['mark_up'] + $priceData['total']['extra_mark_up'];
		$processingFee = $priceData['processing_fee'];

		$profit += $markUp;
		$profit -= $processingFee;

		return ProductQuoteHelper::roundPrice($profit);
	}

	/**
	 * @param FlightQuote $flightQuote
	 * @return float|int|null
	 */
	public function getFinalProfit(FlightQuote $flightQuote)
	{
		$lead = $flightQuote->fqProductQuote->pqProduct->prLead;
		$final = $lead->final_profit;
		if($lead->getAgentsProcessingFee()){
			$final -= $lead->getAgentsProcessingFee();
		}else{
			$final -= ($lead->adults + $lead->children)*Flight::AGENT_PROCESSING_FEE_PER_PAX;
		}
		return $final;
	}

	/**
	 * @param FlightQuote $flightQuote
	 * @return array
	 */
	public static function getBaggageInfo(FlightQuote $flightQuote): array
	{
		//if one segment has baggage -> quote has baggage
		if(!empty($flightQuote->flightQuoteTrips)){
			foreach ($flightQuote->flightQuoteTrips as $trip){
				if(!empty($trip->flightQuoteSegments)){
					foreach ($trip->flightQuoteSegments as $segment){
						if(!empty($segment->flightQuoteSegmentPaxBaggages)){
							foreach ($segment->flightQuoteSegmentPaxBaggages as $baggage){
								if(($baggage->qsb_allow_pieces && $baggage->qsb_allow_pieces > 0)){
									$info = $baggage->qsb_allow_pieces.' pcs';
								} else if ($baggage->qsb_allow_weight){
									$info = $baggage->qsb_allow_weight.$baggage->qsb_allow_unit;
								}

								return ['hasFreeBaggage' => true, 'freeBaggageInfo' => $info ?? null];
							}
						}
					}
				}
			}
		}
		return ['hasFreeBaggage' => false, 'freeBaggageInfo' => $info ?? null];
	}

	/**
	 * @param FlightQuote $flightQuote
	 * @return bool
	 */
	public static function hasAirportChange(FlightQuote $flightQuote): bool
	{
		$result = false;
		if(!empty($flightQuote->flightQuoteTrips)){
			foreach ($flightQuote->flightQuoteTrips as $trip){
				if(!empty($trip->flightQuoteSegments) && count($trip->flightQuoteSegments) > 1){
					$previousSegment = null;
					foreach ($trip->flightQuoteSegments as $segment){
						if($previousSegment !== null && $segment->fqs_departure_airport_iata !== $previousSegment->fqs_arrival_airport_iata){
							$result = true;
							break;
						}
						$previousSegment = $segment;
					}
				}
			}
		}
		return $result;
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

	/**
	 * @param Product $product
	 * @return ActiveDataProvider
	 */
	public static function generateDataProviderForQuoteList(Product $product): ActiveDataProvider
	{
		$query = ProductQuote::find()->where(['pq_product_id' => $product->pr_id]);
		return new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'pq_created_dt' => SORT_DESC,
				]
			],
			'pagination' => [
				'pageSize' => 30,
			],
		]);
	}
}