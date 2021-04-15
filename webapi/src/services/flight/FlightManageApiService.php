<?php

namespace webapi\src\services\flight;

use modules\flight\models\FlightQuoteBooking;
use modules\flight\models\FlightQuoteBookingAirline;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\src\repositories\flightQuoteBookingAirline\FlightQuoteBookingAirlineRepository;
use modules\flight\src\repositories\flightQuoteFlight\FlightQuoteFlightRepository;
use modules\flight\src\repositories\flightQuoteBooking\FlightQuoteBookingRepository;
use webapi\src\forms\flight\FlightRequestApiForm;

/**
 * Class FlightManageApiService
 *
 * @property FlightQuoteFlightRepository $flightQuoteFlightRepository
 * @property FlightQuoteBookingRepository $flightQuoteBookingRepository
 * @property FlightQuoteBookingAirlineRepository $flightQuoteBookingAirlineRepository
 */
class FlightManageApiService
{
    private FlightQuoteFlightRepository $flightQuoteFlightRepository;
    private FlightQuoteBookingRepository $flightQuoteBookingRepository;
    private FlightQuoteBookingAirlineRepository $flightQuoteBookingAirlineRepository;

    public function __construct(
        FlightQuoteFlightRepository $flightQuoteFlightRepository,
        FlightQuoteBookingRepository $flightQuoteBookingRepository,
        FlightQuoteBookingAirlineRepository $flightQuoteBookingAirlineRepository
    ) {
        $this->flightQuoteFlightRepository = $flightQuoteFlightRepository;
        $this->flightQuoteBookingRepository = $flightQuoteBookingRepository;
        $this->flightQuoteBookingAirlineRepository = $flightQuoteBookingAirlineRepository;
    }

    public function handler(FlightRequestApiForm $flightRequestApiForm)
    {
        foreach ($flightRequestApiForm->getFlightApiForms() as $key => $flightApiForm) {
            $flightQuoteFlight = FlightQuoteFlight::create(
                $flightRequestApiForm->flightQuote->getId(),
                null, /* TODO::  */
                null, /* TODO::  */
                self::mapTripType($flightApiForm->flightType),
                $flightApiForm->validatingCarrier,
                null /* TODO::  */
            );
            $flightQuoteFlightId = $this->flightQuoteFlightRepository->save($flightQuoteFlight);

            foreach ($flightApiForm->getBookingInfoForms() as $bookingInfoApiForm) {
                if (!$bookingInfoApiForm->isIssued()) {
                    continue;
                }
                $flightQuoteBooking = FlightQuoteBooking::create(
                    $flightQuoteFlightId,
                    $bookingInfoApiForm->bookingId,
                    $bookingInfoApiForm->pnr,
                    $bookingInfoApiForm->gds,
                    null,
                    $bookingInfoApiForm->validatingCarrier
                );
                $flightQuoteBookingId = $this->flightQuoteBookingRepository->save($flightQuoteBooking);

                foreach ($bookingInfoApiForm->getAirlinesCodeForms() as $airlinesCodeApiForm) {
                    $flightQuoteBookingAirline = FlightQuoteBookingAirline::create(
                        $flightQuoteBookingId,
                        $airlinesCodeApiForm->recordLocator,
                        $airlinesCodeApiForm->code
                    );
                    $this->flightQuoteBookingAirlineRepository->save($flightQuoteBookingAirline);
                }
            }
        }
        /* TODO::  */
    }

    public static function mapTripType(string $tripType)
    {
        return array_search($tripType, FlightQuoteFlight::TRIP_TYPE_LIST);
    }
}
