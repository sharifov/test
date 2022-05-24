<?php

namespace modules\flight\src\helpers;

use common\components\SearchService;
use common\models\Airline;
use common\models\Airports;
use common\models\QuoteSegment;
use DateTime;
use frontend\helpers\JsonHelper;
use frontend\helpers\QuoteHelper;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use modules\flight\src\dto\ngs\QuoteNgsDataDto;
use modules\flight\src\useCases\flightQuote\create\FlightQuotePaxPriceDTO;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuoteCreateForm;
use modules\flight\src\useCases\form\ChangeQuoteCreateForm;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\form\VoluntaryQuoteCreateForm;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuote;
use src\helpers\app\AppHelper;
use src\helpers\product\ProductQuoteHelper;
use src\helpers\setting\SettingHelper;
use modules\flight\src\helpers\SettingHelper as FlightSettingHelper;
use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Class FlightQuoteHelper
 */
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
     * @return FlightQuotePriceDataDTO
     */
    public static function getPricesData(FlightQuote $flightQuote): FlightQuotePriceDataDTO
    {
        /** @var $prices FlightQuotePaxPriceDTO[] */
        $prices = [];
        $service_fee_percent = $flightQuote->getServiceFeePercent();

        $dtoPax = new FlightQuotePaxPriceDataDTO();
        $paxCodeId = null;
        $dtoTotal = new FlightQuoteTotalPriceDTO();
        $paxPrices = FlightQuotePaxPrice::find()->andWhere(['qpp_flight_quote_id' => $flightQuote->fq_id])->orderBy(['qpp_flight_pax_code_id' => SORT_ASC])->all();

        foreach ($paxPrices as $price) {
            $paxCode = FlightPax::getPaxTypeById($price->qpp_flight_pax_code_id);
            if ($dtoPax->paxCodeId !== $price->qpp_flight_pax_code_id) {
                $dtoPax = new FlightQuotePaxPriceDataDTO();
                $dtoPax->paxCodeId = $price->qpp_flight_pax_code_id;
                $dtoPax->paxCode = $paxCode;
            }

            $fare = $price->qpp_fare;
            $dtoPax->fare += $price->qpp_fare;

            $taxes = $price->qpp_tax;
            $dtoPax->taxes += $taxes;

            $net = ($fare + $taxes) * $price->qpp_cnt;
            $dtoPax->net += $net;

            $dtoPax->tickets += $price->qpp_cnt;

            $markUp = $price->qpp_system_mark_up * $price->qpp_cnt;
            $dtoPax->markUp += $markUp;

            $extraMarkUp = $price->qpp_agent_mark_up * $price->qpp_cnt;
            $dtoPax->extraMarkUp += $extraMarkUp;

            $preSelling = $net + $markUp + $extraMarkUp;
            $serviceFee = ProductQuoteHelper::roundPrice($preSelling * $service_fee_percent / 100);
            $dtoPax->serviceFee += $serviceFee;

            $selling = ProductQuoteHelper::roundPrice($serviceFee + $preSelling);
            $dtoPax->selling += $selling;

            $clientSelling = ProductQuoteHelper::roundPrice($selling * $flightQuote->fqProductQuote->pq_client_currency_rate);
            $dtoPax->clientSelling += $clientSelling;

            $prices[$paxCode] = $dtoPax;

            $dtoTotal->tickets += $price->qpp_cnt;
            $dtoTotal->net += $net;
            $dtoTotal->markUp += $markUp;
            $dtoTotal->extraMarkUp += $extraMarkUp;
            $dtoTotal->selling += $selling;
            $dtoTotal->serviceFeeSum += $serviceFee;
            $dtoTotal->clientSelling += $clientSelling;
        }

        $priceDto = new FlightQuotePriceDataDTO();
        $priceDto->prices = $prices;
        $priceDto->total = $dtoTotal;
        $priceDto->serviceFeePercent = $service_fee_percent;
        $priceDto->serviceFee = ($priceDto->serviceFeePercent > 0) ? ($dtoTotal->selling * $priceDto->serviceFeePercent / 100) : 0;
        $priceDto->processingFee = $flightQuote->getProcessingFee();

        return $priceDto;
    }

    /**
     * @param array $priceData
     * @return string
     */
    public static function getEstimationProfitText(FlightQuotePriceDataDTO $priceData): string
    {
        $data = [];
        /* if(isset($priceData['service_fee']) && $priceData['service_fee'] > 0){
            $data[] = '<span class="text-danger">Merchant fee: -'.round($priceData['service_fee'],2).'$</span>';
        } */
        if ($priceData->processingFee > 0) {
            $data[] = '<span class="text-danger">Processing fee: -' . ProductQuoteHelper::roundPrice($priceData->processingFee) . '$</span>';
        }

        return (empty($data)) ? '-' : implode('<br/>', $data);
    }

    /**
     * @param array $priceData
     * @return false|float
     */
    public static function getEstimationProfit(FlightQuotePriceDataDTO $priceData)
    {
        $profit = 0;
        $markUp = $priceData->total->markUp + $priceData->total->extraMarkUp;
        $processingFee = $priceData->processingFee;

        $profit += $markUp;
        $profit -= $processingFee;

        return ProductQuoteHelper::roundPrice($profit);
    }

    /**
     * @param FlightQuote $flightQuote
     * @return float|int|null
     */
    public static function getFinalProfit(FlightQuote $flightQuote)
    {
        $lead = $flightQuote->fqProductQuote->pqProduct->prLead;
        $final = $lead->final_profit;
        if ($lead->getAgentsProcessingFee()) {
            $final -= $lead->getAgentsProcessingFee();
        } else {
            $final -= ($lead->adults + $lead->children) * SettingHelper::processingFee();
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
        if (!empty($flightQuote->flightQuoteTrips)) {
            foreach ($flightQuote->flightQuoteTrips as $trip) {
                if (!empty($trip->flightQuoteSegments)) {
                    foreach ($trip->flightQuoteSegments as $segment) {
                        if (!empty($segment->flightQuoteSegmentPaxBaggages)) {
                            foreach ($segment->flightQuoteSegmentPaxBaggages as $baggage) {
                                if (($baggage->qsb_allow_pieces && $baggage->qsb_allow_pieces > 0)) {
                                    $info = $baggage->qsb_allow_pieces . ' pcs';
                                } elseif ($baggage->qsb_allow_weight) {
                                    $info = $baggage->qsb_allow_weight . $baggage->qsb_allow_unit;
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
        if (!empty($flightQuote->flightQuoteTrips)) {
            foreach ($flightQuote->flightQuoteTrips as $trip) {
                if (!empty($trip->flightQuoteSegments) && count($trip->flightQuoteSegments) > 1) {
                    $previousSegment = null;
                    foreach ($trip->flightQuoteSegments as $segment) {
                        if ($previousSegment !== null && $segment->fqs_departure_airport_iata !== $previousSegment->fqs_arrival_airport_iata) {
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

        if ($flightQuote->fq_origin_search_data) {
            $dataArr = @json_decode($flightQuote->fq_origin_search_data, true);

            if ($dataArr && isset($dataArr['tickets'])) {
                $ticketsArr = $dataArr['tickets'];
                $ticketNr = 1;
                foreach ($ticketsArr as $ticket) {
                    if (!empty($ticket['trips'])) {
                        foreach ($ticket['trips'] as $trip) {
                            if (!empty($trip['segmentIds'])) {
                                foreach ($trip['segmentIds'] as $segmentId) {
                                    $segments[$trip['tripId']][$segmentId] = $ticketNr;
                                }
                            }
                        }
                    }

                    $ticketNr++;
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

        foreach ($quote['trips'] as $trip) {
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

    public static function parseDump($string, $validation = true, &$itinerary = [], $onView = false)
    {
        if (!empty($itinerary) && $validation) {
            $itinerary = [];
        }

        $depCity = $arrCity = null;
        $data = [];
        $segmentCount = 0;
        $operatedCnt = 0;
        try {
            $rows = explode("\n", $string);
            foreach ($rows as $row) {
                $row = trim(preg_replace('!\s+!', ' ', $row));
                $rowArr = explode(' ', $row);
                if (!is_numeric($rowArr[0])) {
                    $rowArrAst = explode('*', $rowArr[0]);
                    if (count($rowArrAst) > 1) {
                        array_shift($rowArr);
                        for ($i = count($rowArrAst) - 1; $i >= 0; $i--) {
                            array_unshift($rowArr, $rowArrAst[$i]);
                        }
                    }
                }

                if (stripos($rowArr[0], "OPERATED") !== false) {
                    $idx = count($itinerary);
                    if ($idx > 0) {
                        $idx--;
                    }
                    if (isset($data[$idx]) && isset($itinerary[$idx])) {
                        $operatedCnt++;
                        $position = stripos($row, "OPERATED BY");
                        $operatedBy = trim(substr($row, $position));
                        $operatedBy = trim(str_ireplace("OPERATED BY", "", $operatedBy));
                        $data[$idx]['operatingAirline'] = $operatedBy;
                        $itinerary[$idx]->operationAirlineCode = $operatedBy;
                    }
                }

                if (!is_numeric((int)$rowArr[0])) {
                    continue;
                }

                $segmentCount++;
                $carrier = substr($rowArr[1], 0, 2);
                $depAirport = '';
                $arrAirport = '';
                $depDate = '';
                $arrDate = '';
                $depDateTime = '';
                $arrDateTime = '';
                $flightNumber = '';
                $arrDateInRow = false;
                $operationAirlineCode = '';

                if (stripos($row, "OPERATED BY") !== false) {
                    $position = stripos($row, "OPERATED BY");
                    $operatedBy = trim(substr($row, $position));
                    $operatedBy = trim(str_ireplace("OPERATED BY", "", $operatedBy));
                    $operationAirlineCode = $operatedBy;
                }

                $posCarr = stripos($row, $carrier);
                $rowFl = substr($row, $posCarr + strlen($carrier));
                preg_match('/([0-9]+)\D/', $rowFl, $matches);
                if (!empty($matches)) {
                    $flightNumber = $matches[1];
                }

                preg_match('/\W([A-Z]{6})\W/', $row, $matches);
                if (!empty($matches)) {
                    $depAirport = substr($matches[1], 0, 3);
                    $arrAirport = substr($matches[1], 3, 3);
                }

                preg_match_all("/[0-9]{2}(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)/", $row, $matches);
                if (!empty($matches)) {
                    if (empty($matches[0])) {
                        continue;
                    }
                    $depDate = $matches[0][0];
                    if (isset($matches[0][1])) {
                        $arrDateInRow = true;
                    }
                    $arrDate = (isset($matches[0][1])) ? $matches[0][1] : $depDate;
                }

                $rowExpl = explode($depAirport . $arrAirport, $row);
                $rowTime = $rowExpl[1];
                preg_match_all('/([0-9]{3,4})(N|A|P)?(\+([0-9])?)?/', $rowTime, $matches);
                if (!empty($matches)) {
                    $now = new DateTime();
                    $matches[1][0] = substr_replace($matches[1][0], ':', -2, 0);
                    $matches[1][1] = substr_replace($matches[1][1], ':', -2, 0);
                    $date = $depDate . ' ' . $matches[1][0];
                    if ($matches[2][0] != '') {
                        $date = $date . strtolower(str_replace('N', 'P', $matches[2][0])) . 'm';
                        $dateFormat = 'jM g:ia';
                    } else {
                        $dateFormat = 'jM H:i';
                    }
                    $depDateTime = DateTime::createFromFormat($dateFormat, $date);
                    if ($depDateTime == false) {
                        continue;
                    }
                    if (
/*$now->format('m') > $depDateTime->format('m')*/
                        $now->getTimestamp() > $depDateTime->getTimestamp()
                    ) {
                        $date = date('Y') + 1 . $date;
                        $dateFormat = 'Y' . $dateFormat;
                        $depDateTime = DateTime::createFromFormat($dateFormat, $date);
                    }

                    $depCity = Airports::findByIata($depAirport);
                    $depTimezone = $depCity ? new \DateTimeZone($depCity->timezone) : null;
                    $depDateTimeWithTimezone = \DateTime::createFromFormat($dateFormat, $date, $depTimezone);


                    $date = $arrDate . ' ' . $matches[1][1];
                    if ($matches[2][1] != '') {
                        $date = $date . strtolower(str_replace('N', 'P', $matches[2][1])) . 'm';
                        $dateFormat = 'jM g:ia';
                    } else {
                        $dateFormat = 'jM H:i';
                    }
                    $arrDateTime = DateTime::createFromFormat($dateFormat, $date);
                    if (
/*$now->format('m') > $arrDateTime->format('m')*/
                        $now->getTimestamp() > $arrDateTime->getTimestamp()
                    ) {
                        $date = date('Y') + 1 . $date;
                        $dateFormat = 'Y' . $dateFormat;
                        $arrDateTime = DateTime::createFromFormat($dateFormat, $date);
                    }
                    $arrDepDiff = $depDateTime->diff($arrDateTime);
                    if ($arrDepDiff->d == 0 && !$arrDateInRow && !empty($matches[3][1])) {
                        if ($matches[3][1] == "+") {
                            $matches[3][1] .= 1;
                        }
                        $arrDateTime->add(\DateInterval::createFromDateString($matches[3][1] . ' day'));
                    }

                    $arrCity = Airports::findByIata($arrAirport);
                    $arrTimezone = $arrCity ? new \DateTimeZone($arrCity->timezone) : null;
                    $arrDateTimeWithTimezone = \DateTime::createFromFormat($dateFormat, $date, $arrTimezone);

                    /*if ($depDateTime > $arrDateTime) {
                        $arrDateTime->add(\DateInterval::createFromDateString('+1 year'));
                    }*/

                    /*$timezone = ($depCity !== null && !empty($depCity->timezone))
                    ? new \DateTimeZone($depCity->timezone)
                    : new \DateTimeZone("UTC");*/
                    /*$timezone = ($arrCity !== null && !empty($arrCity->timezone))
                        ? new \DateTimeZone($arrCity->timezone)
                        : new \DateTimeZone("UTC");*/
                }

                $rowExpl = explode($depDate, $rowFl);
                $cabin = trim(str_replace($flightNumber, '', trim($rowExpl[0])));
                if ($depCity !== null && $arrCity !== null && isset($depDateTimeWithTimezone) && isset($arrDateTimeWithTimezone)) {
                    $flightDuration = intval(($arrDateTimeWithTimezone->getTimestamp() - $depDateTimeWithTimezone->getTimestamp()) / 60);
                } else {
                    $flightDuration = ($arrDateTime->getTimestamp() - $depDateTime->getTimestamp()) / 60;
                }

                $airline = null;
                if (!$onView) {
                    $airline = Airline::findIdentity($carrier);
                }

                $segment = [
                    'carrier' => $carrier,
                    'airlineName' => ($airline !== null)
                        ? $airline->name
                        : $carrier,
                    'departureAirport' => $depAirport,
                    'arrivalAirport' => $arrAirport,
                    'departureDateTime' => $depDateTime,
                    'arrivalDateTime' => $arrDateTime,
                    'flightNumber' => $flightNumber,
                    'bookingClass' => $cabin,
                    'departureCity' => $depCity,
                    'arrivalCity' => $arrCity,
                    'flightDuration' => $flightDuration,
                    'layoverDuration' => 0
                ];
                if ($airline !== null) {
                    $segment['cabin'] = $airline->getCabinByClass($cabin);
                }
                $segment['cabin'] = SearchService::getCabinRealCode($segment['cabin']);
                if (!empty($operationAirlineCode)) {
                    $segment['operatingAirline'] = $operationAirlineCode;
                    $operatingAirline = Airline::findIdentity($operationAirlineCode);
                    $segment['operatingAirlineName'] = $operatingAirline->name ?? $operationAirlineCode;
                }
                if (count($data) != 0 && isset($data[count($data) - 1])) {
                    $previewSegment = $data[count($data) - 1];
                    $segment['layoverDuration'] = ($segment['departureDateTime']->getTimestamp() - $previewSegment['arrivalDateTime']->getTimestamp()) / 60;
                }
                $data[] = $segment;
                $itinerary[] = (new ItineraryDumpDTO([]))->feelByParsedreservationDump($segment);
            }
            if ($validation) {
                //echo sprintf('Check %d - %d - %d', $segmentCount, count($data), $operatedCnt);
                if ($segmentCount !== count($data) + $operatedCnt) {
                    $data = [];
                }
            }
        } catch (ErrorException $ex) {
            $data = [];
        }

        return $data;
    }

    public static function parsePriceDump(string $priceDump): array
    {
        $explodeDump = explode("\n", $priceDump);
        $priceRows = [];
        $bagRows = [];
        $validatingCarrierRow = '';
        foreach ($explodeDump as $key => $row) {
            $row = trim($row);
            if (stripos($row, "«") !== false) {
                continue;
            }

            if (
                (stripos($row, "JCB") !== false ||
                    stripos($row, "ADT") !== false ||
                    stripos($row, "PFA") !== false ||
                    stripos($row, "JNN") !== false ||
                    stripos($row, "CNN") !== false ||
                    stripos($row, "CBC") !== false ||
                    stripos($row, "JNF") !== false ||
                    stripos($row, "INF") !== false ||
                    stripos($row, "CBI") !== false) &&
                stripos($row, "XT") !== false
            ) {
                $priceRows[] = $row;
            }

            if (stripos($row, "VALIDATING CARRIER") !== false && empty($validatingCarrierRow)) {
                $row = str_replace("VALIDATING CARRIER - ", "", $row);
                $validating = explode(' ', $row);
                $validatingCarrierRow = $validating[0];
            }

            if (stripos($row, "BAG ALLOWANCE") !== false) {
                $bagRows[] = self::getBagString($explodeDump, $key);
            }
        }

        $prices = [];
        foreach ($priceRows as $row) {
            if (
                stripos($row, "JCB") !== false ||
                stripos($row, "ADT") !== false ||
                stripos($row, "PFA") !== false
            ) {
                if (empty($prices[FlightPax::PAX_ADULT])) {
                    $prices[FlightPax::PAX_ADULT] = self::getPrice($row);
                }
            } elseif (
                stripos($row, "JNN") !== false ||
                stripos($row, "CNN") !== false ||
                stripos($row, "CBC") !== false
            ) {
                if (empty($prices[FlightPax::PAX_CHILD])) {
                    $prices[FlightPax::PAX_CHILD] = self::getPrice($row);
                }
            } elseif (
                stripos($row, "JNF") !== false ||
                stripos($row, "INF") !== false ||
                stripos($row, "CBI") !== false
            ) {
                if (empty($prices[FlightPax::PAX_INFANT])) {
                    $prices[FlightPax::PAX_INFANT] = self::getPrice($row);
                }
            }
        }

        return [
            'validating_carrier' => $validatingCarrierRow,
            'prices' => $prices,
            'baggage' => $bagRows
        ];
    }

    private static function getBagString($array, $index): array
    {
        $bags = [];
        foreach ($array as $key => $val) {
            $val = trim($val);
            if ($key < $index) {
                continue;
            }
            if (stripos($val, "BAG ALLOWANCE") !== false && $key > $index) {
                break;
            }
            $bags[] = $val;
            if (stripos($val, "**") !== false) {
                if (!isset($array[($key + 1)]) || stripos($array[($key + 1)], "2NDCHECKED") === false) {
                    break;
                }
            }
        }

        $bagsString = explode('2NDCHECKED', trim(implode(' ', $bags)));
        $bags = [
            'segment' => '',
            'free_baggage' => [],
            'paid_baggage' => []
        ];

        foreach ($bagsString as $key => $val) {
            $val = str_replace('*', '', $val);
            $detail = explode('-', $val);

            if (stripos($val, "BAG ALLOWANCE") !== false) {
                $bags['segment'] = $detail[1];
                if (
                    stripos($val, "NIL/") !== false ||
                    stripos($val, "*/") !== false
                ) {
                    if (stripos($val, "1STCHECKED") !== false) {
                        $bagsString = explode('1STCHECKED', $val);
                        $detailBag = explode('/', $bagsString[1]);
                        if (stripos($detailBag[0], "USD") !== false) {
                            $bagItem = [
                                'ordinal' => '1st',
                                'piece' => 1,
                                'weight' => 'N/A',
                                'height' => 'N/A',
                                'price' => explode('-', $detailBag[0])[2],
                            ];
                            $detailVolume = explode('UP TO', $bagsString[1]);
                            if (isset($detailVolume[1])) {
                                $bagItem['weight'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[1])));
                            }
                            if (isset($detailVolume[2])) {
                                $bagItem['height'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[2])));
                            }
                            $bags['paid_baggage'][] = $bagItem;
                        }
                    }
                } else {
                    $detailBag = explode('/', $detail[2]);
                    $bags['free_baggage'] = [
                        'piece' => (int)str_replace('P', '', $detailBag[0]),
                        'weight' => 'N/A',
                        'height' => 'N/A',
                        'price' => 'USD0'
                    ];
                    $detailVolume = explode('UP TO', $detail[2]);
                    if (isset($detailVolume[1])) {
                        $bags['free_baggage']['weight'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[1])));
                    }
                    if (isset($detailVolume[2])) {
                        $bags['free_baggage']['height'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[2])));
                    }
                }
            } else {
                $detailBag = explode('/', $detail[2]);
                if (stripos($detailBag[0], "USD") !== false) {
                    $bagItem = [
                        'ordinal' => '2nd',
                        'piece' => 1,
                        'weight' => 'N/A',
                        'height' => 'N/A',
                        'price' => $detailBag[0],
                    ];

                    $detailVolume = explode('UP TO', $detail[2]);
                    if (isset($detailVolume[1])) {
                        $bagItem['weight'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[1])));
                    }
                    if (isset($detailVolume[2])) {
                        $bagItem['height'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[2])));
                    }
                    $bags['paid_baggage'][] = $bagItem;
                }
            }
        }
        return $bags;
    }

    private static function getPrice($string): array
    {
        $arr = [
            'fare' => 0,
            'taxes' => 0,
        ];
        $rows = explode(' ', $string);
        foreach ($rows as $row) {
            if (stripos($row, "XT") !== false) {
                $arr['taxes'] = (float)str_replace('XT', '', $row);
            }
        }
        $lastRow = end($rows);
        if (stripos($lastRow, "USD") !== false) {
            $lastRow = str_replace('USD', '', $lastRow);
            $arr['fare'] = (float)substr($lastRow, 0, -2) - $arr['taxes'];
        }
        return $arr;
    }

    public static function quoteCalculatePaxPrices(FlightQuoteCreateForm $model): void
    {
        $oldPrices = unserialize($model->oldPrices);
        foreach ($oldPrices as $oldPrice) {
            foreach ($model->prices as $newPrice) {
                if ((int)$oldPrice['paxCodeId'] === (int)$newPrice->paxCodeId) {
                    if ((float)$oldPrice['selling'] !== (float)$newPrice->selling) {
                        $serviceFee = ProductQuoteHelper::roundPrice((float)$newPrice->selling * $model->serviceFee / 100);
                        $newPrice->markup = (float)$newPrice->selling - $serviceFee;

                        if ((int)$newPrice->selling === 0) {
                            $newPrice->net = 0.00;
                            $newPrice->fare = 0.00;
                            $newPrice->taxes = 0.00;
                            $newPrice->markup = 0.00;
                            $newPrice->clientSelling = 0.00;
                        } else {
                            $newPrice->selling = $newPrice->markup + (float)$newPrice->fare + (float)$newPrice->taxes;
                            $serviceFee = ProductQuoteHelper::roundPrice((float)$newPrice->selling * $model->serviceFee / 100);
                            $newPrice->selling = ProductQuoteHelper::roundPrice(((float)$newPrice->selling + $serviceFee) * (int)$newPrice->cnt);
                            $newPrice->clientSelling = ProductQuoteHelper::roundPrice((float)$newPrice->selling * $model->currencyRate);
                        }
                    } else {
                        $newPrice->net = (float)$newPrice->fare + (float)$newPrice->taxes;
                        $newPrice->selling = (float)$newPrice->net + (float)$newPrice->markup;

                        $serviceFee = ProductQuoteHelper::roundPrice((float)$newPrice->selling * $model->serviceFee / 100);
                        $newPrice->selling = ProductQuoteHelper::roundPrice(((float)$newPrice->selling + $serviceFee) * (int)$newPrice->cnt);
                        $newPrice->clientSelling = ProductQuoteHelper::roundPrice((float)$newPrice->selling * $model->currencyRate);
                    }
                }
            }
        }

        $model->oldPrices = serialize(ArrayHelper::toArray($model->prices));
    }

    public static function getTripsSegmentsData(string $reservationDump, string $cabinClass, int $tripType): array
    {
        $trips = [];
        $segments = [];
        $segmentCount = 0;
        $operatedCnt = 0;

        $rows = explode("\n", $reservationDump);
        foreach ($rows as $row) {
            $row = trim(preg_replace('!\s+!', ' ', $row));
            $rowArr = explode(' ', $row);
            if (!is_numeric($rowArr[0])) {
                $rowArrAst = explode('*', $rowArr[0]);
                if (count($rowArrAst) > 1) {
                    array_shift($rowArr);
                    for ($i = count($rowArrAst) - 1; $i >= 0; $i--) {
                        array_unshift($rowArr, $rowArrAst[$i]);
                    }
                }
            }

            if (stripos($rowArr[0], "OPERATED") !== false) {
                $idx = count($segments);
                if ($idx > 0) {
                    $idx--;
                }
                if (isset($segments[$idx])) {
                    $operatedCnt++;
                    $position = stripos($row, "OPERATED BY");
                    $operatedBy = trim(substr($row, $position));
                    $operatedBy = trim(str_ireplace("OPERATED BY", "", $operatedBy));
                    preg_match('/\((.*?)\)/', $operatedBy, $matches);
                    if (!empty($matches)) {
                        $operatedBy = trim($matches[1]);
                    }
                    if (mb_strlen($operatedBy) > 2) {
                        $airline = Airline::find()->andWhere(['like' ,'name', $operatedBy ])->one();
                        if (!empty($airline)) {
                            $operatedBy = $airline->iata;
                        }
                    }
                    $segments[$idx]['operatingAirline'] = str_replace('/', '', $operatedBy);
                }
            }

            if (!is_numeric(intval($rowArr[0]))) {
                continue;
            }

            $segmentCount++;
            $carrier = isset($rowArr[1]) ? substr($rowArr[1], 0, 2) : '';
            $depAirport = '';
            $arrAirport = '';
            $depDate = '';
            $arrDate = '';
            $depDateTime = '';
            $arrDateTime = '';
            $flightNumber = '';
            $arrDateInRow = false;
            $operationAirlineCode = '';

            if (stripos($row, "OPERATED BY") !== false) {
                $position = stripos($row, "OPERATED BY");
                $operatedBy = trim(substr($row, $position));
                $operatedBy = trim(str_ireplace("OPERATED BY", "", $operatedBy));
                preg_match('/\((.*?)\)/', $operatedBy, $matches);
                if (!empty($matches)) {
                    $operatedBy = trim($matches[1]);
                }
                if (mb_strlen($operatedBy) > 2) {
                    $airline = Airline::find()->andWhere(['like' ,'name', $operatedBy ])->one();
                    if (!empty($airline)) {
                        $operatedBy = $airline->iata;
                    }
                }
                $operationAirlineCode = str_replace('/', '', $operatedBy);
            }

            $posCarr = stripos($row, $carrier);
            $rowFl = substr($row, $posCarr + strlen($carrier));

            preg_match('/([0-9]+)\D/', $rowFl, $matches);
            if (!empty($matches)) {
                $flightNumber = $matches[1];
            }

            preg_match('/\W([A-Z]{6})\W/', $row, $matches);
            if (!empty($matches)) {
                $depAirport = substr($matches[1], 0, 3);
                $arrAirport = substr($matches[1], 3, 3);
            }

            preg_match_all("/[0-9]{2}(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)/", $row, $matches);
            if (!empty($matches)) {
                if (empty($matches[0])) {
                    continue;
                }
                $depDate = $matches[0][0];
                if (isset($matches[0][1])) {
                    $arrDateInRow = true;
                }
                $arrDate = (isset($matches[0][1])) ? $matches[0][1] : $depDate;
            }

            $rowExpl = explode($depAirport . $arrAirport, $row);
            $rowTime = $rowExpl[1];
            preg_match_all('/([0-9]{3,4})(N|A|P)?(\+([0-9])?)?/', $rowTime, $matches);
            if (!empty($matches)) {
                $now = new DateTime();
                $matches[1][0] = substr_replace($matches[1][0], ':', -2, 0);
                $matches[1][1] = substr_replace($matches[1][1], ':', -2, 0);
                $date = $depDate . ' ' . $matches[1][0];
                if ($matches[2][0] != '') {
                    $date = $date . strtolower(str_replace('N', 'P', $matches[2][0])) . 'm';
                    $dateFormat = 'jM g:ia';
                } else {
                    $dateFormat = 'jM H:i';
                }
                $depDateTime = DateTime::createFromFormat($dateFormat, $date);
                if ($depDateTime == false) {
                    continue;
                }
                if (
/*$now->format('m') > $depDateTime->format('m')*/
                    $now->getTimestamp() > $depDateTime->getTimestamp()
                ) {
                    $date = date('Y') + 1 . $date;
                    $dateFormat = 'Y' . $dateFormat;
                    $depDateTime = DateTime::createFromFormat($dateFormat, $date);
                }

                $depCity = Airports::findByIata($depAirport);
                $depTimezone = $depCity ? new \DateTimeZone($depCity->timezone) : null;
                $depDateTimeWithTimezone = \DateTime::createFromFormat($dateFormat, $date, $depTimezone);


                $date = $arrDate . ' ' . $matches[1][1];
                if ($matches[2][1] != '') {
                    $date = $date . strtolower(str_replace('N', 'P', $matches[2][1])) . 'm';
                    $dateFormat = 'jM g:ia';
                } else {
                    $dateFormat = 'jM H:i';
                }
                $arrDateTime = DateTime::createFromFormat($dateFormat, $date);
                if (
/*$now->format('m') > $arrDateTime->format('m')*/
                    $now->getTimestamp() > $arrDateTime->getTimestamp()
                ) {
                    $date = date('Y') + 1 . $date;
                    $dateFormat = 'Y' . $dateFormat;
                    $arrDateTime = DateTime::createFromFormat($dateFormat, $date);
                }

                $arrCity = Airports::findByIata($arrAirport);
                $arrTimezone = $arrCity ? new \DateTimeZone($arrCity->timezone) : null;
                $arrDateTimeWithTimezone = \DateTime::createFromFormat($dateFormat, $date, $arrTimezone);


                $arrDepDiff = $depDateTime->diff($arrDateTime);
                if ($arrDepDiff->d == 0 && !$arrDateInRow && !empty($matches[3][1])) {
                    if ($matches[3][1] == "+") {
                        $matches[3][1] .= 1;
                    }
                    $arrDateTime->add(\DateInterval::createFromDateString($matches[3][1] . ' day'));
                }
                /*if ($depDateTime > $arrDateTime) {
                    $arrDateTime->add(\DateInterval::createFromDateString('+1 year'));
                }*/
                /*$timezone = ($depCity !== null && !empty($depCity->timezone))
                ? new \DateTimeZone($depCity->timezone)
                : new \DateTimeZone("UTC");*/
                /*$timezone = ($arrCity !== null && !empty($arrCity->timezone))
                    ? new \DateTimeZone($arrCity->timezone)
                    : new \DateTimeZone("UTC");*/
            }

            $cabin = trim(str_replace($flightNumber, '', trim($rowExpl[0])));
            if ($depCity !== null && $arrCity !== null && isset($depDateTimeWithTimezone) && isset($arrDateTimeWithTimezone)) {
                $flightDuration = intval(($arrDateTimeWithTimezone->getTimestamp() - $depDateTimeWithTimezone->getTimestamp()) / 60);
            } else {
                $flightDuration = ($arrDateTime->getTimestamp() - $depDateTime->getTimestamp()) / 60;
            }

            $airline = Airline::findIdentity($carrier);

            $segment = [
                'carrier' => $carrier,
                'airlineName' => ($airline !== null)
                    ? $airline->name
                    : $carrier,
                'departureAirport' => $depAirport,
                'arrivalAirport' => $arrAirport,
                'departureDateTime' => $depDateTime,
                'arrivalDateTime' => $arrDateTime,
                'flightNumber' => $flightNumber,
                'bookingClass' => $cabin,
                'departureCity' => $depCity,
                'arrivalCity' => $arrCity,
                'flightDuration' => $flightDuration,
                'layoverDuration' => 0
            ];
//          $segment['key'] = '#'.$segment['qs_flight_number'].$segment['qs_departure_airport_code'].'-'.$segment['qs_arrival_airport_code'].' '.$segment['qs_departure_time']->format('Y-m-d H:i');

            if (!empty($airline)) {
                $segment['cabin'] = QuoteSegment::getCabinReal($airline->getCabinByClass($cabin));
            }
            if (!isset($segment['cabin'])) {
                $segment['cabin'] = QuoteSegment::getCabinReal($cabinClass);
            }
            $segments[] = $segment;
        }

        $tripIndex = 0;
        foreach ($segments as $key => $segment) {
            if ($tripType !== Flight::TRIP_TYPE_ONE_WAY) {
                if ($key != 0) {
                    $lastSegment = $segments[$key - 1] ?? $segments[$key];
                    $isMoreOneDay = self::isMoreOneDay($lastSegment['arrivalDateTime'], $segment['departureDateTime']);
                    if ($isMoreOneDay) {
                        ++$tripIndex;
                    }
                }
            }
//          $segment['departureDateTime'] = $segment['departureDateTime']->format('Y-m-d H:i');
//          $segment['arrivalDateTime'] = $segment['arrivalDateTime']->format('Y-m-d H:i');
            $trips[$tripIndex]['segments'][] = $segment;
        }
        Yii::info('my27', 'getTripsFromDump');
        foreach ($trips as $key => $trip) {
            $firstSegment = $trip['segments'][0];
            $lastSegment = $trip['segments'][count($trip['segments']) - 1];

            $depCity = Airports::findByIata($firstSegment['departureAirport']);
            $arrCity = Airports::findByIata($lastSegment['arrivalAirport']);
            $arrivalTime = $lastSegment['arrivalDateTime'];
            $departureTime = $firstSegment['departureDateTime'];

            $depTimezone = $depCity ? new \DateTimeZone($depCity->timezone) : null;
            $depDateTimeWithTimezone = new \DateTime($departureTime, $depTimezone);

            $arrTimezone = $arrCity ? new \DateTimeZone($arrCity->timezone) : null;
            $arrDateTimeWithTimezone = new \DateTime($arrivalTime, $arrTimezone);

            if ($depCity !== null && $arrCity !== null) {
                $trips[$key]['duration'] = intval(($arrDateTimeWithTimezone->getTimestamp() - $depDateTimeWithTimezone->getTimestamp()) / 60);
            } else {
                $trips[$key]['duration'] = ($arrivalTime->getTimestamp() - $departureTime->getTimestamp()) / 60;
            }

//          $keySegment = [];
            foreach ($trip['segments'] as $segmentKey => $segment) {
                $trips[$key]['segments'][$segmentKey] = ArrayHelper::toArray((new ItineraryDumpDTO([]))->feelByParsedreservationDump($segment));
            }
//          $trips[$key]['qt_key'] = implode('|', $keySegment);
        }

        return $trips;
    }

    public static function getMetaInfo(FlightQuote $flightQuote): ?array
    {
        if (($originSearchData = self::getJsonOriginSearchData($flightQuote))) {
            return $originSearchData['meta'] ?? null;
        }
        return null;
    }

    public static function getPenaltiesInfo(FlightQuote $flightQuote): ?array
    {
        if ($originSearchData = self::getJsonOriginSearchData($flightQuote)) {
            return $originSearchData['penalties'] ?? null;
        }
        return null;
    }

    public static function getJsonOriginSearchData(FlightQuote $flightQuote): ?array
    {
        if (!empty($flightQuote->fq_origin_search_data)) {
            try {
                return JsonHelper::decode($flightQuote->fq_origin_search_data);
            } catch (\Throwable $throwable) {
                Yii::error(
                    AppHelper::throwableFormatter($throwable),
                    'FlightQuoteHelper:getJsonOriginSearchData:failed'
                );
            }
        }
        return null;
    }

    public static function formattedRanking(?array $meta, string $class = 'text-info'): string
    {
        if (!empty($meta['rank'])) {
            $rank = number_format($meta['rank'], 1, '.', '');
            $rank = ($rank === '10.0') ? 10 : $rank;

            return '<span class="' . $class . '"
                data-toggle="tooltip"
                title="Rank: ' . $meta['rank'] . '">
                    <i class="fa fa-border">' . $rank . '</i>
            </span>';
        }
        return '';
    }

    public static function formattedCheapest(?array $meta, string $class = 'text-success'): string
    {
        if (!empty($meta['cheapest'])) {
            return '<span class="' . $class . '"
                data-toggle="tooltip"                
                title="Cheapest">
                    <i class="fa fa-money fa-border"></i>
            </span>';
        }
        return '';
    }

    public static function formattedFastest(?array $meta, string $class = 'text-warning'): string
    {
        if (!empty($meta['fastest'])) {
            return '<span class="' . $class . '"
                data-toggle="tooltip"
                title="Fastest">
                    <i class="fa fa-rocket fa-border"></i>
            </span>';
        }
        return '';
    }

    public static function formattedBest(?array $meta, string $class = 'text-primary'): string
    {
        if (!empty($meta['best'])) {
            return '<span class="' . $class . '"
                data-toggle="tooltip"
                data-html="true"
                title="Best">
                    <i class="fa fa-thumbs-o-up fa-border"></i>
            </span>';
        }
        return '';
    }

    public static function formattedPenalties(?array $penalties, string $class = 'text-warning'): string
    {
        if ($penalties && QuoteHelper::checkPenaltiesInfo($penalties)) {
            return '<span class="' . $class . '"
                data-toggle="tooltip"
                data-html="true"
                title="' . QuoteHelper::innerPenalties($penalties) . '">
                    <i class="fa fa-expand fa-border"></i>
            </span>';
        }
        return '';
    }

    public static function formattedNgs(QuoteNgsDataDto $ngsDto): string
    {
        if (!empty($ngsDto->name)) {
            return '<span
                data-toggle="tooltip"
                data-html="true"
                title="' . self::displayNgsList($ngsDto->list) . '">
                ' . $ngsDto->name . '
            </span>';
        }
        return '';
    }

    public static function displayNgsList(array $ngsList): string
    {
        $out = '';
        if ($ngsList) {
            $out .= "<div class='tooltip_quote_info_box'>";
            $out .= '<p>NGS Features Name: </p>';

            $out .= '<ul>';
            foreach ($ngsList as $item) {
                if (isset($item['commercialName']) && $item['commercialName']) {
                    $out .= '<li><strong>' . Html::encode($item['commercialName']) . '</strong></li>';
                }
            }
            $out .= '</ul>';
            $out .= '</div>';
        }
        return $out;
    }

    public static function formattedFreeBaggage(?array $meta, string $class = 'success'): string
    {
        if (!empty($meta['bags'])) {
            return '<span class="' . $class . '"
                data-toggle="tooltip"
                title="Free baggage - ' . (int) $meta['bags'] .  ' pcs">
                <i class="fa fa-suitcase fa-border"></i>
                <span class="flight_inside_icon">' . (int) $meta['bags'] . '</span>
            </span>';
        }
        return '';
    }

    public static function getNgsDtoOfSelectedQuote(FlightQuote $flightQuote): QuoteNgsDataDto
    {
        $flightQuoteData = Json::decode($flightQuote->fq_origin_search_data);

        if (isset($flightQuoteData['key']) && self::generateHashQuoteKey($flightQuoteData['key']) === $flightQuote->fq_hash_key) {
            return new QuoteNgsDataDto($flightQuoteData['ngsFeatures'] ?? []);
        }

        if (isset($flightQuoteData['ngsItineraries'])) {
            foreach ($flightQuoteData['ngsItineraries'] as $ngsItinerary) {
                if (self::generateHashQuoteKey($ngsItinerary['key']) === $flightQuote->fq_hash_key) {
                    return new QuoteNgsDataDto($ngsItinerary['ngsFeatures'] ?? []);
                }
            }
        }
        return new QuoteNgsDataDto();
    }

    public static function getMainAirline(FlightQuote $flightQuote): array
    {
        $result = [];
        if ($flightQuote->flightQuoteFlights) {
            foreach ($flightQuote->flightQuoteFlights as $key => $flightQuoteFlight) {
                $result[$key]['code'] = $flightQuoteFlight->fqf_main_airline ?? '';
                $result[$key]['name'] = $flightQuoteFlight->mainAirline->name ?? '';
            }
            return $result;
        }
        $result[0]['code'] = $flightQuote->fq_main_airline ?? '';
        $result[0]['name'] = $flightQuote->mainAirline->name ?? '';

        return $result;
    }

    public static function isNextTrip(array $prevSegment, array $curSegment): bool
    {
        if ((!$prevArrivalDateTime = $prevSegment['arrivalDateTime'] ?? null) || !($prevArrivalDateTime instanceof DateTime)) {
            throw new \RuntimeException('ArrivalDateTime is corrupted');
        }
        if ((!$departureDateTime = $curSegment['departureDateTime'] ?? null) || !($departureDateTime instanceof DateTime)) {
            throw new \RuntimeException('DepartureDateTime is corrupted');
        }
        if (self::isMoreOneDay($prevArrivalDateTime, $departureDateTime)) {
            return true;
        }

        if ((!$prevArrivalAirport = $prevSegment['arrivalAirport'] ?? null) || !is_string($prevArrivalAirport)) {
            throw new \RuntimeException('ArrivalAirport is corrupted');
        }
        if ((!$departureAirport = $curSegment['departureAirport'] ?? null) || !is_string($departureAirport)) {
            throw new \RuntimeException('DepartureAirport is corrupted');
        }
        if (!self::isEqualLocation($prevArrivalAirport, $departureAirport)) {
            return true;
        }
        return false;
    }

    /**
     * @param FlightQuote $flightQuote
     * @return DateTime
     */
    public static function getLastDepartureDate(FlightQuote $flightQuote): DateTime
    {
        try {
            $flightQuoteSegments = $flightQuote->flightQuoteSegments;
            return new DateTime(end($flightQuoteSegments)->fqs_departure_dt);
        } catch (\Throwable $throwable) {
            Yii::error(
                AppHelper::throwableFormatter($throwable),
                'FlightQuoteHelper:getLastFlightDate:failed'
            );
            return new DateTime();
        }
    }


    /**
     * Returns the expiration date for a new quote (exchange, refund)
     *
     * @param FlightQuote $flightQuote
     * @return string
     */
    public static function getExpirationDate(FlightQuote $flightQuote): string
    {
        $maxDate = self::getLastDepartureDate($flightQuote);
        $maxDate = $maxDate->modify(sprintf('-%d hours', FlightSettingHelper::getMinHoursDifferenceOffers()));
        $date = (new DateTime())->modify(sprintf('+%d days', FlightSettingHelper::getExpirationDaysOfNewOffers()));

        return ($date > $maxDate) ? $maxDate->format('Y-m-d') : $date->format('Y-m-d');
    }

    private static function isEqualLocation(string $prevArrivalAirport, string $departureAirport): bool
    {
        return $prevArrivalAirport === $departureAirport;
    }

    private static function isMoreOneDay(DateTime $departureDateTime, DateTime $arrivalDateTime): bool
    {
        $diff = $departureDateTime->diff($arrivalDateTime);
        return (int) sprintf('%d%d%d', $diff->y, $diff->m, $diff->d) >= 1;
    }
}
