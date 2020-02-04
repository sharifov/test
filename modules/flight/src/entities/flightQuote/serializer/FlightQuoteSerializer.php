<?php

namespace modules\flight\src\entities\flightQuote\serializer;

use modules\flight\models\FlightQuote;
use sales\entities\serializer\Serializer;

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
        ];
    }

    public function getData(): array
    {
        $data =  $this->toArray();

        if ($this->model->fqFlight) {
            $data['flight'] = $this->model->fqFlight->serialize();
        }

        if ($this->model->flightQuoteSegments) {
            $data['segments'] = [];
            foreach ($this->model->flightQuoteSegments as $segment) {
                $data['segments'][] = $segment->serialize();
            }
        }

        if ($this->model->flightQuotePaxPrices) {
            $data['pax_prices'] = [];
            foreach ($this->model->flightQuotePaxPrices as $price) {
                $data['pax_prices'][] = $price->serialize();
            }
        }

        return $data;
    }
}
