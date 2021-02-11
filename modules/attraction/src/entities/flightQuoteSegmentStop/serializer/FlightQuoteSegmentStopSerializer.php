<?php

namespace modules\flight\src\entities\flightQuoteSegmentStop\serializer;

use modules\flight\models\FlightQuoteSegmentStop;
use sales\entities\serializer\Serializer;

/**
 * Class FlightQuoteSegmentStopSerializer
 *
 * @property FlightQuoteSegmentStop $model
 */
class FlightQuoteSegmentStopSerializer extends Serializer
{
    public function __construct(FlightQuoteSegmentStop $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'qss_quote_segment_id',
            'qss_location_iata',
            'qss_equipment',
            'qss_elapsed_time',
            'qss_duration',
            'qss_departure_dt',
            'qss_arrival_dt',
        ];
    }

    public function getData(): array
    {
        return $this->toArray();
    }
}
