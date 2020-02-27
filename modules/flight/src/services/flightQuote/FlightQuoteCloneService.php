<?php

namespace modules\flight\src\services\flightQuote;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteSegmentPaxBaggage;
use modules\flight\models\FlightQuoteSegmentPaxBaggageCharge;
use modules\flight\models\FlightQuoteSegmentStop;
use modules\flight\models\FlightQuoteTrip;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
use modules\flight\src\repositories\FlightQuoteSegmentPaxBaggageChargeRepository\FlightQuoteSegmentPaxBaggageChargeRepository;
use modules\flight\src\repositories\flightQuoteSegmentPaxBaggageRepository\FlightQuoteSegmentPaxBaggageRepository;
use modules\flight\src\repositories\flightQuoteSegmentStopRepository\FlightQuoteSegmentStopRepository;
use modules\flight\src\repositories\flightQuoteTripRepository\FlightQuoteTripRepository;

/**
 * Class FlightQuoteCloneService
 *
 * @property FlightRepository $flightRepository
 * @property FlightQuoteRepository $flightQuoteRepository
 * @property FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
 * @property FlightQuoteSegmentRepository $flightQuoteSegmentRepository
 * @property FlightQuoteTripRepository $flightQuoteTripRepository
 * @property FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository
 * @property FlightQuoteSegmentPaxBaggageChargeRepository $flightQuoteSegmentPaxBaggageChargeRepository
 * @property FlightQuoteSegmentStopRepository $flightQuoteSegmentStopRepository
 */
class FlightQuoteCloneService
{
    private $flightRepository;
    private $flightQuoteRepository;
    private $flightQuotePaxPriceRepository;
    private $flightQuoteSegmentRepository;
    private $flightQuoteTripRepository;
    private $flightQuoteSegmentPaxBaggageRepository;
    private $flightQuoteSegmentPaxBaggageChargeRepository;
    private $flightQuoteSegmentStopRepository;

    public function __construct(
        FlightRepository $flightRepository,
        FlightQuoteRepository $flightQuoteRepository,
        FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository,
        FlightQuoteSegmentRepository $flightQuoteSegmentRepository,
        FlightQuoteTripRepository $flightQuoteTripRepository,
        FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository,
        FlightQuoteSegmentPaxBaggageChargeRepository $flightQuoteSegmentPaxBaggageChargeRepository,
        FlightQuoteSegmentStopRepository $flightQuoteSegmentStopRepository
    )
    {
        $this->flightRepository = $flightRepository;
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->flightQuotePaxPriceRepository = $flightQuotePaxPriceRepository;
        $this->flightQuoteSegmentRepository = $flightQuoteSegmentRepository;
        $this->flightQuoteTripRepository = $flightQuoteTripRepository;
        $this->flightQuoteSegmentPaxBaggageRepository = $flightQuoteSegmentPaxBaggageRepository;
        $this->flightQuoteSegmentPaxBaggageChargeRepository = $flightQuoteSegmentPaxBaggageChargeRepository;
        $this->flightQuoteSegmentStopRepository = $flightQuoteSegmentStopRepository;
    }

    public function clone(int $originalQuoteId, int $toFlightId, int $toProductQuoteId): void
    {
        $originalQuote = $this->flightQuoteRepository->find($originalQuoteId);
        $toFlight = $this->flightRepository->find($toFlightId);

        $quote = FlightQuote::clone($originalQuote, $toFlight->fl_id, $toProductQuoteId);
        $this->flightQuoteRepository->save($quote);

        foreach ($originalQuote->flightQuotePaxPrices as $originalPaxPrice) {
            $paxPrice = FlightQuotePaxPrice::clone($originalPaxPrice, $quote->fq_id);
            $this->flightQuotePaxPriceRepository->save($paxPrice);
        }

        $tripsMap = [];
        foreach ($originalQuote->flightQuoteTrips as $originalTrip) {
            $trip = FlightQuoteTrip::clone($originalTrip, $quote->fq_id);
            $tripId = $this->flightQuoteTripRepository->save($trip);
            $tripsMap[$originalTrip->fqt_id] = $tripId;
        }

        foreach ($originalQuote->flightQuoteSegments as $originalSegment) {

            $tripId = $tripsMap[$originalSegment->fqs_flight_quote_trip_id] ?? null;
            $segment = FlightQuoteSegment::clone($originalSegment, $quote->fq_id, $tripId);
            $this->flightQuoteSegmentRepository->save($segment);

            foreach ($originalSegment->flightQuoteSegmentPaxBaggages as $originalBaggage) {
                $baggage = FlightQuoteSegmentPaxBaggage::clone($originalBaggage, $segment->fqs_id);
                $this->flightQuoteSegmentPaxBaggageRepository->save($baggage);
            }

            foreach ($originalSegment->flightQuoteSegmentPaxBaggageCharges as $originalBaggageCharges) {
                $baggageCharges = FlightQuoteSegmentPaxBaggageCharge::clone($originalBaggageCharges, $segment->fqs_id);
                $this->flightQuoteSegmentPaxBaggageChargeRepository->save($baggageCharges);
            }

            foreach ($originalSegment->flightQuoteSegmentStops as $originalStop) {
                $stop = FlightQuoteSegmentStop::clone($originalStop, $segment->fqs_id);
                $this->flightQuoteSegmentStopRepository->save($stop);
            }
        }
    }
}
