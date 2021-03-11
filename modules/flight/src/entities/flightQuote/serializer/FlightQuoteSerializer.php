<?php

namespace modules\flight\src\entities\flightQuote\serializer;

use modules\flight\models\FlightQuote;
use sales\entities\serializer\Serializer;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteSerializer
 *
 * @property FlightQuote $model
 */
class FlightQuoteSerializer extends Serializer
{
    public function __construct(FlightQuote $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'fq_flight_id',
            'fq_source_id',
            'fq_product_quote_id',
            'fq_gds',
            'fq_gds_pcc',
            'fq_gds_offer_id',
            'fq_type_id',
            'fq_cabin_class',
            'fq_trip_type_id',
            'fq_main_airline',
            'fq_fare_type_id',
            'fq_last_ticket_date',
            'fq_origin_search_data',
            'fq_json_booking',
            'fq_ticket_json'
        ];
    }

    public function getData(): array
    {
        $data =  $this->toArray();
        $data['fq_type_name'] = FlightQuote::getTypeName($this->model->fq_type_id);
        $data['fq_fare_type_name'] = FlightQuote::getFareTypeNameById($this->model->fq_fare_type_id);

        if ($this->model->fqFlight) {
            $data['flight'] = $this->model->fqFlight->serialize();
        }

        if ($this->model->flightQuoteTrips) {
            $data['trips'] = [];
            foreach ($this->model->flightQuoteTrips as $flightQuoteTrip) {
                $trip = $flightQuoteTrip->serialize();
                foreach ($flightQuoteTrip->flightQuoteSegments as $flightQuoteSegment) {
                    $trip['segments'][] = $flightQuoteSegment->serialize();
                }
                $data['trips'][] = $trip;
            }
        }

        if ($this->model->flightQuotePaxPrices) {
            $data['pax_prices'] = [];
            foreach ($this->model->flightQuotePaxPrices as $price) {
                $data['pax_prices'][] = $price->serialize();
            }
        }

        if ($this->model->fqFlight->flightPaxes) {
            $data['paxes'] = [];
            foreach ($this->model->fqFlight->flightPaxes as $flightPax) {
                $data['paxes'][] = $flightPax->serialize();
            }
        }

        return $data;
    }
}
