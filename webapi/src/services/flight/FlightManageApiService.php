<?php

namespace webapi\src\services\flight;

use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuoteBooking;
use modules\flight\models\FlightQuoteBookingAirline;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\FlightQuoteTicket;
use modules\flight\src\repositories\flightPaxRepository\FlightPaxRepository;
use modules\flight\src\repositories\flightQuoteBookingAirline\FlightQuoteBookingAirlineRepository;
use modules\flight\src\repositories\flightQuoteFlight\FlightQuoteFlightRepository;
use modules\flight\src\repositories\flightQuoteBooking\FlightQuoteBookingRepository;
use modules\flight\src\repositories\flightQuoteTicket\FlightQuoteTicketRepository;
use webapi\src\forms\flight\FlightRequestApiForm;
use yii\helpers\ArrayHelper;

/**
 * Class FlightManageApiService
 *
 * @property FlightQuoteFlightRepository $flightQuoteFlightRepository
 * @property FlightQuoteBookingRepository $flightQuoteBookingRepository
 * @property FlightQuoteBookingAirlineRepository $flightQuoteBookingAirlineRepository
 * @property FlightPaxRepository $flightPaxRepository
 * @property FlightQuoteTicketRepository $flightQuoteTicketRepository
 */
class FlightManageApiService
{
    private FlightQuoteFlightRepository $flightQuoteFlightRepository;
    private FlightQuoteBookingRepository $flightQuoteBookingRepository;
    private FlightQuoteBookingAirlineRepository $flightQuoteBookingAirlineRepository;
    private FlightPaxRepository $flightPaxRepository;
    private FlightQuoteTicketRepository $flightQuoteTicketRepository;

    /**
     * @param FlightQuoteFlightRepository $flightQuoteFlightRepository
     * @param FlightQuoteBookingRepository $flightQuoteBookingRepository
     * @param FlightQuoteBookingAirlineRepository $flightQuoteBookingAirlineRepository
     * @param FlightPaxRepository $flightPaxRepository
     * @param FlightQuoteTicketRepository $flightQuoteTicketRepository
     */
    public function __construct(
        FlightQuoteFlightRepository $flightQuoteFlightRepository,
        FlightQuoteBookingRepository $flightQuoteBookingRepository,
        FlightQuoteBookingAirlineRepository $flightQuoteBookingAirlineRepository,
        FlightPaxRepository $flightPaxRepository,
        FlightQuoteTicketRepository $flightQuoteTicketRepository
    ) {
        $this->flightQuoteFlightRepository = $flightQuoteFlightRepository;
        $this->flightQuoteBookingRepository = $flightQuoteBookingRepository;
        $this->flightQuoteBookingAirlineRepository = $flightQuoteBookingAirlineRepository;
        $this->flightPaxRepository = $flightPaxRepository;
        $this->flightQuoteTicketRepository = $flightQuoteTicketRepository;
    }

    public function handler(FlightRequestApiForm $flightRequestApiForm): void
    {
        $flightPaxProcessed = [];
        $this->flightPaxRepository->removePaxByFlight($flightRequestApiForm->flightQuote->fq_flight_id);

        foreach ($flightRequestApiForm->getFlightApiForms() as $key => $flightApiForm) {
            $flightQuoteFlight = FlightQuoteFlight::create(
                $flightRequestApiForm->flightQuote->getId(),
                self::mapTripType($flightApiForm->flightType),
                $flightApiForm->validatingCarrier,
                $flightApiForm->uniqueId,
                $flightApiForm->status,
                $flightApiForm->pnr,
                $flightApiForm->validatingCarrier,
                $flightApiForm->getOriginalDataJson()
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

                foreach ($bookingInfoApiForm->getPassengerForms() as $passengerApiForm) {
                    if (ArrayHelper::keyExists($passengerApiForm->getHashIdentity(), $flightPaxProcessed)) {
                        $flightPax = $flightPaxProcessed[$passengerApiForm->getHashIdentity()];
                    } else {
                        $flightPax = FlightPax::createByParams(
                            $flightRequestApiForm->flightQuote->fq_flight_id,
                            $passengerApiForm->paxType,
                            $passengerApiForm->first_name,
                            $passengerApiForm->last_name,
                            $passengerApiForm->middle_name,
                            $passengerApiForm->birth_date,
                            $passengerApiForm->gender,
                            $passengerApiForm->nationality
                        );
                        $this->flightPaxRepository->save($flightPax);
                        $flightPaxProcessed[$passengerApiForm->getHashIdentity()] = $flightPax;
                    }
                    $flightQuoteTicket = FlightQuoteTicket::create($flightPax->fp_id, $flightQuoteBookingId, $passengerApiForm->tktNumber);
                    $this->flightQuoteTicketRepository->save($flightQuoteTicket);
                }
            }
        }
    }

    /**
     * @param string $tripType
     * @return false|int|string
     */
    private static function mapTripType(string $tripType)
    {
        return array_search($tripType, FlightQuoteFlight::TRIP_TYPE_LIST);
    }
}
