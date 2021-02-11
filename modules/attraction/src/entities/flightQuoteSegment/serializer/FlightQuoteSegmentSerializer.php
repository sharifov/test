<?php

namespace modules\flight\src\entities\flightQuoteSegment\serializer;

use modules\flight\models\FlightQuoteSegment;
use sales\entities\serializer\Serializer;

/**
 * Class FlightQuoteSegmentSerializer
 *
 * @property FlightQuoteSegment $model
 */
class FlightQuoteSegmentSerializer extends Serializer
{
    public function __construct(FlightQuoteSegment $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'fqs_departure_dt',
            'fqs_arrival_dt',
            'fqs_stop',
            'fqs_flight_number',
            'fqs_booking_class',
            'fqs_duration',
            'fqs_departure_airport_iata',
            'fqs_departure_airport_terminal',
            'fqs_arrival_airport_iata',
            'fqs_arrival_airport_terminal',
            'fqs_operating_airline',
            'fqs_marketing_airline',
            'fqs_air_equip_type',
            'fqs_marriage_group',
            'fqs_cabin_class',
            'fqs_meal',
            'fqs_fare_code',
            'fqs_ticket_id',
            'fqs_recheck_baggage',
            'fqs_mileage'
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();

        if ($this->model->flightQuoteSegmentStops) {
            $data['stops'] = [];
            foreach ($this->model->flightQuoteSegmentStops as $stop) {
                $data['stops'][] = $stop->serialize();
            }
        }

        if ($this->model->flightQuoteSegmentPaxBaggages) {
            $data['baggages'] = [];
            foreach ($this->model->flightQuoteSegmentPaxBaggages as $baggage) {
                $data['baggages'][] = $baggage->serialize();
            }
        }

        if ($this->model->flightQuoteSegmentPaxBaggageCharges) {
            $data['baggage_charges'] = [];
            foreach ($this->model->flightQuoteSegmentPaxBaggageCharges as $charge) {
                $data['baggage_charges'][] = $charge->serialize();
            }
        }

        return $data;
    }
}
