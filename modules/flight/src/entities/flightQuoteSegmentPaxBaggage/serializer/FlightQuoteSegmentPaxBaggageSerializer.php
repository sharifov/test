<?php

namespace modules\flight\src\entities\flightQuoteSegmentPaxBaggage\serializer;

use modules\flight\models\FlightQuoteSegmentPaxBaggage;
use sales\entities\serializer\Serializer;

/**
 * Class FlightQuoteSegmentPaxBaggageSerializer
 *
 * @property FlightQuoteSegmentPaxBaggage $model
 */
class FlightQuoteSegmentPaxBaggageSerializer extends Serializer
{
    public function __construct(FlightQuoteSegmentPaxBaggage $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'qsb_flight_pax_code_id',
            'qsb_flight_quote_segment_id',
            'qsb_airline_code',
            'qsb_allow_pieces',
            'qsb_allow_weight',
            'qsb_allow_unit',
            'qsb_allow_max_weight',
            'qsb_allow_max_size',
        ];
    }

    public function getData(): array
    {
        return $this->toArray();
    }
}
