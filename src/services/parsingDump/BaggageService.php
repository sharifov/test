<?php

namespace src\services\parsingDump;

use common\models\Airports;
use common\models\QuotePrice;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuoteSegment;
use src\services\parsingDump\lib\ParsingDump;
use src\forms\segment\SegmentBaggageForm;

/**
 * Class BaggageService
 */
class BaggageService
{
    public string $gds;
    public array $baggageFromDump;

    public const TYPE_FREE = 'free';
    public const TYPE_PAID = 'paid';

    public const TYPE_LIST = [
        self::TYPE_FREE => 'Free',
        self::TYPE_PAID => 'Paid'
    ];

    /**
     * @param string $gds
     */
    public function __construct(string $gds)
    {
        $this->gds = ParsingDump::setGdsForParsing($gds);
    }

    /**
     * @param $dump
     * @return $this
     */
    public function setBaggageFromDump($dump): BaggageService
    {
        $parserBaggage = ParsingDump::initClass($this->gds, ParsingDump::PARSING_TYPE_BAGGAGE);
        $this->baggageFromDump = $parserBaggage->parseDump($dump);
        return $this;
    }

    public function getBaggageFromDump(): array
    {
        return $this->baggageFromDump;
    }

    /**
     * @param array $segments
     * @return array
     */
    public function attachBaggageToSegments(array $segments): array
    {
        foreach ($segments as $key => $segment) {
            if ($baggageSource = $this->searchByIata($segment)) {
                $baggage = [];

                if (!empty($baggageSource['paid_baggage'])) {
                    foreach ($baggageSource['paid_baggage'] as $item) {
                        $segmentBaggageForm = new SegmentBaggageForm();
                        $segmentBaggageForm->segmentIata = $segment['segmentIata'];
                        $segmentBaggageForm->type = self::TYPE_PAID;
                        $segmentBaggageForm->piece = $item['piece'];
                        $segmentBaggageForm->weight = $item['weight'];
                        $segmentBaggageForm->height = $item['height'];
                        $segmentBaggageForm->price = $item['price'];
                        $segmentBaggageForm->currency = $item['currency'];

                        $baggage[] = $segmentBaggageForm;
                    }
                }
                if (!empty($baggageSource['free_baggage'])) {
                    $segmentBaggageForm = new SegmentBaggageForm();
                    $segmentBaggageForm->segmentIata = $segment['segmentIata'];
                    $segmentBaggageForm->type = self::TYPE_FREE;
                    $segmentBaggageForm->piece = $baggageSource['free_baggage']['piece'];
                    $segmentBaggageForm->weight = $baggageSource['free_baggage']['weight'];
                    $segmentBaggageForm->height = $baggageSource['free_baggage']['height'];
                    $segmentBaggageForm->price = $baggageSource['free_baggage']['price'];
                    $segmentBaggageForm->currency = $baggageSource['free_baggage']['currency'];

                    $baggage[] = $segmentBaggageForm;
                }
                $segments[$key]['baggage'] = $baggage;
            }
        }
        return $segments;
    }

    public static function generateBaggageFromFlightSegment(FlightQuoteSegment $flightQuoteSegment): array
    {
        $iataKey = $flightQuoteSegment->fqs_departure_airport_iata . $flightQuoteSegment->fqs_arrival_airport_iata;
        $serialized = $flightQuoteSegment->serialize();
        $result = [];

        if (!empty($serialized['baggages'])) {
            foreach ($serialized['baggages'] as $baggageSource) {
                $segmentBaggageForm = new SegmentBaggageForm();
                $segmentBaggageForm->segmentIata = $iataKey;
                $segmentBaggageForm->type = self::TYPE_FREE;
                $segmentBaggageForm->piece = $baggageSource['qsb_allow_pieces'];
                $segmentBaggageForm->weight = $baggageSource['qsb_allow_max_weight'];
                $segmentBaggageForm->height = $baggageSource['qsb_allow_max_size'];
                $segmentBaggageForm->paxCode = FlightPax::getPaxTypeById($baggageSource['qsb_flight_pax_code_id']);

                $result[] = $segmentBaggageForm;
            }
        }
        if (!empty($serialized['baggage_charges'])) {
            foreach ($serialized['baggage_charges'] as $baggageSource) {
                $segmentBaggageForm = new SegmentBaggageForm();
                $segmentBaggageForm->segmentIata = $iataKey;
                $segmentBaggageForm->type = self::TYPE_PAID;
                $segmentBaggageForm->piece = $baggageSource['qsbc_first_piece'];
                $segmentBaggageForm->weight = $baggageSource['qsbc_max_weight'];
                $segmentBaggageForm->height = $baggageSource['qsbc_max_size'];
                $segmentBaggageForm->paxCode = FlightPax::getPaxTypeById($baggageSource['qsbc_flight_pax_code_id']);
                $segmentBaggageForm->price = $baggageSource['qsbc_client_price'];
                $segmentBaggageForm->currency = $baggageSource['qsbc_client_currency'];

                $result[] = $segmentBaggageForm;
            }
        }
        return $result;
    }

    /**
     * @param array $segment
     * @return array|null
     */
    public function searchByIata(array $segment): ?array
    {
        $segmentIata = $segment['departureAirport'] . $segment['arrivalAirport'];

        if (!empty($this->baggageFromDump['baggage'])) {
            foreach ($this->baggageFromDump['baggage'] as $key => $baggageItem) {
                if ($baggageItem['segment'] === $segmentIata) {
                    $baggage = $this->baggageFromDump['baggage'][$key];
                    unset($this->baggageFromDump['baggage'][$key]);
                    break;
                }
                $baggageDeparture = substr($baggageItem['segment'], 0, 3);
                $baggageArrival = substr($baggageItem['segment'], 3, 3);

                if (
                    ($segment['departureCity']->city === Airports::getCityByIata($baggageDeparture))
                    &&
                    ($segment['arrivalCity']->city === Airports::getCityByIata($baggageArrival))
                ) {
                    $baggage = $this->baggageFromDump['baggage'][$key];
                    unset($this->baggageFromDump['baggage'][$key]);
                    break;
                }
            }
        }
        return $baggage ?? null;
    }

    /**
     * @param string|null $cost
     * @return float
     */
    public static function prepareCost(?string $cost): float
    {
        $cost = $cost ?? '0.00';
        $cost = preg_replace('/[^.0-9]/', '', $cost);
        return (new QuotePrice())->roundValue($cost);
    }
}
