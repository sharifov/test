<?php

namespace modules\flight\src\entities\flightQuoteFlight\serializer;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use src\entities\serializer\Serializer;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteSerializer
 *
 * @property FlightQuoteFlight $model
 */
class FlightQuoteFlightSerializer extends Serializer
{
    /**
     * @param FlightQuoteFlight $model
     */
    public function __construct(FlightQuoteFlight $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'fqf_trip_type_id',
            'fqf_main_airline',
            'fqf_booking_id',
            'fqf_pnr',
            'fqf_validating_carrier',
        ];
    }

    public function getData(bool $withBooking = true): array
    {
        $data = $this->toArray();

        if ($withBooking) {
            $data['bookings'] = [];
            if ($this->model->flightQuoteBookings) {
                foreach ($this->model->flightQuoteBookings as $keyBooking => $flightQuoteBooking) {
                    $data['bookings'][$keyBooking] = $flightQuoteBooking->serialize();
                }
            }
        }

        $flightQuote = $this->model->fqfFq;
        $data['flight_quote']['fq_type_name'] = FlightQuote::getTypeName($flightQuote->fq_type_id);
        $data['flight_quote']['fq_fare_type_name'] = FlightQuote::getFareTypeNameById($flightQuote->fq_fare_type_id);
        $data['flight'] = $flightQuote->fqFlight ? $flightQuote->fqFlight->serialize() : [];

        $data['trips'] = [];
        if ($this->model->flightQuoteTrips) {
            foreach ($this->model->flightQuoteTrips as $keyTrip => $flightQuoteTrip) {
                $data['trips'][$keyTrip] = $flightQuoteTrip->serialize();
                foreach ($flightQuoteTrip->flightQuoteSegments as $keySegment => $flightQuoteSegment) {
                    ArrayHelper::setValue(
                        $data,
                        'trips.' . $keyTrip . '.segments.' . $keySegment,
                        $flightQuoteSegment->serialize()
                    );
                }
            }
        }

        return $data;
    }
}
